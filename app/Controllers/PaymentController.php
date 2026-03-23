<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Controller;
use App\Models\Order;
use App\Services\PaymentService;

class PaymentController extends Controller
{
    public function show(string $id): void
    {
        $order = (new Order())->find((int) $id);
        if (!$order) {
            $this->response->setStatus(404);
            echo 'Pedido não encontrado.';
            return;
        }
        $this->view('orders/payment', ['order' => $order, 'flash' => App::getInstance()?->session()->getFlash('message')]);
    }

    public function initiate(string $id): void
    {
        try {
            $order = (new Order())->find((int) $id);
            if (!$order) {
                throw new \RuntimeException('Pedido não encontrado.');
            }
            (new PaymentService())->initiateMpesaPayment($order['client_phone'], (float) $order['amount'], (int) $order['id']);
            App::getInstance()?->session()->flash('message', 'Pagamento iniciado. Aguarde a confirmação do M-Pesa.');
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('message', 'Não foi possível iniciar o pagamento: ' . $exception->getMessage());
        }
        $this->redirect("/pedido/{$id}/pagamento");
    }
}
