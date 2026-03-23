<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function show(string $id): void
    {
        $order = (new Order())->withRelations((int) $id);
        if (!$order) {
            $this->response->setStatus(404);
            echo 'Pedido não encontrado.';
            return;
        }

        $this->view('orders/payment', [
            'order' => $order,
            'transaction' => (new Transaction())->latestByOrder((int) $id),
        ]);
    }

    public function initiate(string $id): void
    {
        try {
            $order = (new Order())->find((int) $id);
            if (!$order) {
                throw new \RuntimeException('Pedido não encontrado.');
            }

            $result = (new PaymentService())->initiateMpesaPayment($order['client_phone'], (float) $order['amount'], (int) $order['id']);
            $message = !empty($result['reused'])
                ? 'Já existe uma transação pendente para este pedido. Aguarde a confirmação ou clique em verificar pagamento.'
                : 'Cobrança M-Pesa enviada. Confirme no telemóvel e depois use “verificar pagamento”.';
            $this->redirectWithFlash("/pedido/{$id}/pagamento", ['success' => $message]);
        } catch (\Throwable $exception) {
            $this->redirectWithFlash("/pedido/{$id}/pagamento", ['error' => $exception->getMessage()]);
        }
    }

    public function verify(string $id): void
    {
        try {
            $transaction = (new Transaction())->latestByOrder((int) $id);
            if (!$transaction) {
                throw new \RuntimeException('Ainda não existe transação iniciada para este pedido.');
            }

            (new PaymentService())->syncOrderPaymentState($transaction['debito_reference']);
            $updated = (new Transaction())->findByReference($transaction['debito_reference']);
            $this->redirectWithFlash("/pedido/{$id}/pagamento", ['success' => 'Status atualizado para: ' . ($updated['status'] ?? 'desconhecido') . '.']);
        } catch (\Throwable $exception) {
            $this->redirectWithFlash("/pedido/{$id}/pagamento", ['error' => $exception->getMessage()]);
        }
    }
}
