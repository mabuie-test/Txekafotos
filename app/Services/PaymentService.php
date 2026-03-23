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
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Pedido não encontrado para pagamento.');
        }

        if (in_array($order['status'], ['pago', 'em_edicao', 'revisao', 'concluido', 'aprovado'], true)) {
            throw new RuntimeException('Este pedido já possui pagamento confirmado e não pode iniciar nova cobrança.');
        }

        $this->assertGatewayConfiguration();
        $normalizedPhone = $this->normalizePhone($phone);
        if (!$this->validateMozambiqueMpesaNumber($normalizedPhone)) {
            throw new RuntimeException('Número M-Pesa inválido. Use um contacto Vodacom Moçambique válido.');
        }

        $existingPending = $this->transactions->activeByOrder($orderId);
        if ($existingPending) {
            return [
                'transaction_id' => (int) $existingPending['id'],
                'debito_reference' => $existingPending['debito_reference'],
                'response' => json_decode((string) $existingPending['raw_response'], true) ?: [],
                'reused' => true,
            ];
        }

        if ($this->transactions->completedByOrder($orderId)) {
            throw new RuntimeException('O pagamento deste pedido já foi liquidado.');
        }

        $referenceDescription = sprintf('Pedido Foto #%d', $orderId);
        $payload = [
            'msisdn' => $normalizedPhone,
            'amount' => round($amount, 2),
            'reference_description' => $referenceDescription,
        ];

        $walletId = (string) config('payment.wallet_id');
        $endpoint = rtrim((string) config('payment.base_url'), '/') . "/wallets/{$walletId}/c2b/mpesa";
        $response = $this->request('POST', $endpoint, $payload);
        $reference = $this->extractGatewayReference($response);

        $db = Database::instance();
        $db->beginTransaction();

        try {
            $transactionId = $this->createPendingTransaction($orderId, $reference, (float) $payload['amount'], $normalizedPhone, $referenceDescription, [
                'request' => $payload,
                'response' => $response,
                'phase' => 'initiation',
            ]);

            if (in_array($order['status'], ['pendente_pagamento', 'falhou_pagamento'], true)) {
                $this->orderService->transitionStatus($orderId, 'pagamento_em_analise');
            }

            $this->auditService->log('system', null, 'payment.initiated', 'transaction', $transactionId, 'Pagamento M-Pesa iniciado.', [
                'order_id' => $orderId,
                'debito_reference' => $reference,
                'msisdn' => $normalizedPhone,
            ]);
            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->pdo()->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }

        return [
            'transaction_id' => $transactionId,
            'debito_reference' => $reference,
            'response' => $response,
            'reused' => false,
        ];
    }

    public function checkPaymentStatus(string $debitoReference): array
    {
        $this->assertGatewayConfiguration();
        $endpoint = rtrim((string) config('payment.base_url'), '/') . "/transactions/{$debitoReference}/status";
        return $this->request('GET', $endpoint);
    }

    public function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if ($digits === '') {
            return '';
        }

        if (strlen($digits) === 9 && str_starts_with($digits, '8')) {
            $digits = '258' . $digits;
        }

        if (strlen($digits) === 8 && str_starts_with($digits, '8')) {
            $digits = '258' . $digits;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '258' . substr($digits, 1);
        }

        if (!str_starts_with($digits, '258') && strlen($digits) <= 9) {
            $digits = '258' . $digits;
        }

        return $digits;
    }

    public function validateMozambiqueMpesaNumber(string $phone): bool
    {
        return (bool) preg_match('/^2588[4-7][0-9]{7}$/', $phone);
    }

    public function createPendingTransaction(int $orderId, string $reference, float $amount, string $msisdn, string $referenceDescription, array $response): int
    {
        return $this->transactions->create([
            'order_id' => $orderId,
            'debito_reference' => $reference,
            'payment_method' => (string) config('payment.method', 'mpesa'),
            'amount' => $amount,
            'status' => 'pending',
            'msisdn' => $msisdn,
            'reference_description' => $referenceDescription,
            'gateway_status' => 'initiated',
            'failure_reason' => null,
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

        if ($transaction['status'] === 'completed') {
            return;
        }

        $db = Database::instance();
        $db->beginTransaction();

        try {
            $this->transactions->updateGatewayState((int) $transaction['id'], 'completed', $this->extractGatewayStatus($response), [
                'response' => $response,
                'phase' => 'sync',
            ]);
            $order = $this->orders->find((int) $transaction['order_id']);
            if ($order) {
                if ($order['status'] === 'falhou_pagamento') {
                    $this->orders->markPaymentUnderReview((int) $transaction['order_id']);
                    $order['status'] = 'pagamento_em_analise';
                }
                if (in_array($order['status'], ['pendente_pagamento', 'pagamento_em_analise'], true)) {
                    $this->orderService->transitionStatus((int) $transaction['order_id'], 'pago');
                }
            }
            $this->orders->markPaymentConfirmed((int) $transaction['order_id']);
            $this->financeService->registerIncome((int) $transaction['order_id'], (int) $transaction['id'], (float) $transaction['amount'], 'Pagamento confirmado via M-Pesa.');
            $this->auditService->log('system', null, 'payment.completed', 'transaction', (int) $transaction['id'], 'Pagamento confirmado.', ['reference' => $reference]);
            $db->commit();
        } catch (\Throwable $exception) {
            if ($db->pdo()->inTransaction()) {
                $db->rollBack();
            }
            throw $exception;
        }
    }

    public function markTransactionFailed(string $reference, array $response): void
    {
        $transaction = $this->transactions->findByReference($reference);
        if (!$transaction) {
            throw new RuntimeException('Transação não encontrada para falha.');
        }

        $gatewayStatus = $this->extractGatewayStatus($response);
        $failureReason = (string) ($response['message'] ?? $response['error'] ?? $response['reason'] ?? 'Pagamento não autorizado.');

        $this->transactions->updateGatewayState((int) $transaction['id'], 'failed', $gatewayStatus, [
            'response' => $response,
            'phase' => 'sync',
        ], $failureReason);

        $order = $this->orders->find((int) $transaction['order_id']);
        if ($order && in_array($order['status'], ['pendente_pagamento', 'pagamento_em_analise'], true)) {
            $this->orders->markPaymentFailed((int) $transaction['order_id']);
        }

        $this->auditService->log('system', null, 'payment.failed', 'transaction', (int) $transaction['id'], 'Pagamento falhou.', [
            'reference' => $reference,
            'reason' => $failureReason,
        ]);
    }

    public function syncOrderPaymentState(string $reference): array
    {
        $transaction = $this->transactions->findByReference($reference);
        if (!$transaction) {
            throw new RuntimeException('Transação não encontrada para sincronização.');
        }

        $statusResponse = $this->checkPaymentStatus($reference);
        $mappedStatus = $this->mapGatewayStatus($statusResponse);

        return match ($mappedStatus) {
            'completed' => tap($statusResponse, fn () => $this->markTransactionCompleted($reference, $statusResponse)),
            'failed' => tap($statusResponse, fn () => $this->markTransactionFailed($reference, $statusResponse)),
            default => tap($statusResponse, fn () => $this->transactions->updateGatewayState((int) $transaction['id'], 'processing', $this->extractGatewayStatus($statusResponse), [
                'response' => $statusResponse,
                'phase' => 'sync',
            ])),
        };
    }

    public function syncPendingTransactions(): array
    {
        $results = [];
        foreach ($this->transactions->pending() as $transaction) {
            $results[] = [
                'reference' => $transaction['debito_reference'],
                'status' => $this->mapGatewayStatus($this->syncOrderPaymentState($transaction['debito_reference'])),
            ];
        }

        return $results;
    }

    private function request(string $method, string $url, ?array $payload = null): array
    {
        $headers = [
            'Authorization: Bearer ' . (string) config('payment.token'),
            'Content-Type: application/json',
            'Accept: application/json',
            'User-Agent: Txekafotos/1.0',
        ];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => (int) config('payment.timeout', 30),
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        if ($payload !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        $raw = curl_exec($ch);
        if ($raw === false) {
            $message = curl_error($ch);
            curl_close($ch);
            throw new RuntimeException('Falha de comunicação com a API Débito: ' . $message);
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

    private function assertGatewayConfiguration(): void
    {
        foreach (['payment.base_url', 'payment.token', 'payment.wallet_id'] as $configKey) {
            if (trim((string) config($configKey, '')) === '') {
                throw new RuntimeException('Configuração da API Débito incompleta. Preencha DEBITO_BASE_URL, DEBITO_TOKEN e DEBITO_WALLET_ID.');
            }
        }
    }

    private function extractGatewayReference(array $response): string
    {
        $reference = (string) ($response['reference'] ?? $response['debito_reference'] ?? $response['transaction_reference'] ?? $response['id'] ?? '');
        if ($reference === '') {
            throw new RuntimeException('A API Débito não retornou uma referência de transação válida.');
        }

        return $reference;
    }

    private function extractGatewayStatus(array $response): string
    {
        return strtolower((string) ($response['status'] ?? $response['transaction_status'] ?? $response['state'] ?? 'unknown'));
    }

    private function mapGatewayStatus(array $response): string
    {
        return match ($this->extractGatewayStatus($response)) {
            'success', 'successful', 'completed', 'paid' => 'completed',
            'failed', 'error', 'cancelled', 'rejected', 'declined' => 'failed',
            default => 'processing',
        };
    }
}
