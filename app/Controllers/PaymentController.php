<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
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
            'flash' => App::getInstance()?->session()->getFlash('message'),
            'error' => App::getInstance()?->session()->getFlash('error'),
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
            App::getInstance()?->session()->flash('message', $message);
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('error', $exception->getMessage());
        }

        $this->redirect("/pedido/{$id}/pagamento");
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
            App::getInstance()?->session()->flash('message', 'Status atualizado para: ' . ($updated['status'] ?? 'desconhecido') . '.');
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('error', $exception->getMessage());
        }

        $this->redirect("/pedido/{$id}/pagamento");
    }
}
