<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Services\OrderService;

class TrackingController extends Controller
{
    public function form(): void
    {
        $this->view('tracking/form');
    }

    public function lookup(): void
    {
        $order = (new Order())->findByTracking((int) $this->request->input('order_id'), (string) $this->request->input('client_phone'));
        if (!$order) {
            $this->view('tracking/form', ['error' => 'Pedido não encontrado com os dados informados.']);
            return;
        }
        $this->redirect('/pedido/' . $order['id'] . '/status');
    }

    public function status(string $id): void
    {
        $order = (new Order())->withRelations((int) $id);
        if (!$order) {
            $this->response->setStatus(404);
            echo 'Pedido não encontrado.';
            return;
        }
        $this->view('tracking/status', ['order' => $order]);
    }

    public function approve(string $id): void
    {
        try {
            (new OrderService())->approve((int) $id);
            $this->redirect('/pedido/' . $id . '/status');
        } catch (\Throwable $exception) {
            $this->view('tracking/status', ['order' => (new Order())->withRelations((int) $id), 'error' => $exception->getMessage()]);
        }
    }
}
