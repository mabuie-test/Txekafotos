<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Order;
use App\Models\Transaction;
use RuntimeException;

class PaymentService
{
    public function __construct(
        private readonly Transaction $transactions = new Transaction(),
        private readonly Order $orders = new Order(),
        private readonly FinanceService $financeService = new FinanceService(),
        private readonly AuditService $auditService = new AuditService(),
        private readonly OrderService $orderService = new OrderService(),
    ) {
    }

    public function initiateMpesaPayment(string $phone, float $amount, int $orderId): array
    {
        $normalizedPhone = $this->normalizePhone($phone);
        if (!$this->validateMozambiqueMpesaNumber($normalizedPhone)) {
            throw new RuntimeException('Número M-Pesa inválido. Use um número válido de Moçambique.');
        }

        $walletId = (string) config('payment.wallet_id');
        $endpoint = rtrim((string) config('payment.base_url'), '/') . "/wallets/{$walletId}/c2b/mpesa";
        $payload = [
            'msisdn' => $normalizedPhone,
            'amount' => $amount,
            'reference_description' => "Pedido Foto #{$orderId}",
        ];

        $response = $this->request('POST', $endpoint, $payload);
        $reference = (string) ($response['reference'] ?? $response['debito_reference'] ?? $response['transaction_reference'] ?? '');
        if ($reference === '') {
            throw new RuntimeException('A API Débito não retornou uma referência de transação válida.');
        }

        $transactionId = $this->createPendingTransaction($orderId, $reference, $amount, $response);
        Database::instance()->execute("UPDATE orders SET status = 'pagamento_em_analise' WHERE id = :id", ['id' => $orderId]);
        $this->auditService->log('system', null, 'payment.initiated', 'transaction', $transactionId, 'Pagamento M-Pesa iniciado.', ['order_id' => $orderId, 'debito_reference' => $reference]);

        return ['transaction_id' => $transactionId, 'debito_reference' => $reference, 'response' => $response];
    }

    public function checkPaymentStatus(string $debitoReference): array
    {
        $endpoint = rtrim((string) config('payment.base_url'), '/') . "/transactions/{$debitoReference}/status";
        return $this->request('GET', $endpoint);
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '0')) {
            $digits = '258' . substr($digits, 1);
        }
        if (!str_starts_with($digits, '258')) {
            $digits = '258' . $digits;
        }
        return $digits;
    }

    public function validateMozambiqueMpesaNumber(string $phone): bool
    {
        return (bool) preg_match('/^2588[4-7][0-9]{7}$/', $phone);
    }

    public function createPendingTransaction(int $orderId, string $reference, float $amount, array $response): int
    {
        return $this->transactions->create([
            'order_id' => $orderId,
            'debito_reference' => $reference,
            'payment_method' => (string) config('payment.method', 'mpesa'),
            'amount' => $amount,
            'status' => 'pending',
            'raw_response' => json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'last_checked_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function markTransactionCompleted(string $reference, array $response): void
    {
        $transaction = $this->transactions->findByReference($reference);
        if (!$transaction) {
            throw new RuntimeException('Transação não encontrada para conclusão.');
        }

        $this->transactions->updateStatusByReference($reference, 'completed', $response);
        Database::instance()->execute("UPDATE orders SET status = 'pago', payment_confirmed_at = NOW() WHERE id = :id", ['id' => $transaction['order_id']]);
        $this->financeService->registerIncome((int) $transaction['order_id'], (int) $transaction['id'], (float) $transaction['amount'], 'Pagamento confirmado via M-Pesa.');
        $this->auditService->log('system', null, 'payment.completed', 'transaction', (int) $transaction['id'], 'Pagamento confirmado.', ['reference' => $reference]);
    }

    public function markTransactionFailed(string $reference, array $response): void
    {
        $transaction = $this->transactions->findByReference($reference);
        if (!$transaction) {
            throw new RuntimeException('Transação não encontrada para falha.');
        }

        $this->transactions->updateStatusByReference($reference, 'failed', $response);
        Database::instance()->execute("UPDATE orders SET status = 'falhou_pagamento' WHERE id = :id", ['id' => $transaction['order_id']]);
        $this->auditService->log('system', null, 'payment.failed', 'transaction', (int) $transaction['id'], 'Pagamento falhou.', ['reference' => $reference]);
    }

    public function syncOrderPaymentState(string $reference): array
    {
        $statusResponse = $this->checkPaymentStatus($reference);
        $status = strtolower((string) ($statusResponse['status'] ?? $statusResponse['transaction_status'] ?? 'pending'));

        return match ($status) {
            'success', 'completed', 'paid' => tap($statusResponse, fn () => $this->markTransactionCompleted($reference, $statusResponse)),
            'failed', 'error', 'cancelled' => tap($statusResponse, fn () => $this->markTransactionFailed($reference, $statusResponse)),
            default => tap($statusResponse, fn () => $this->transactions->updateStatusByReference($reference, 'processing', $statusResponse)),
        };
    }

    private function request(string $method, string $url, ?array $payload = null): array
    {
        $headers = [
            'Authorization: Bearer ' . (string) config('payment.token'),
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => (int) config('payment.timeout', 30),
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            throw new RuntimeException('Falha de comunicação com a API Débito: ' . curl_error($ch));
        }

        $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Resposta inválida recebida da API Débito.');
        }
        if ($statusCode >= 400) {
            throw new RuntimeException('Erro da API Débito: ' . ($decoded['message'] ?? $raw));
        }
        return $decoded;
    }
}
