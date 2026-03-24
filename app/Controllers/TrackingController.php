<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;

class TrackingController extends Controller
{
    public function form(): void
    {
        $this->view('tracking/form', [
            'contactPhone' => (string) config('app.contact_phone', ''),
            'contactEmail' => (string) config('app.contact_email', ''),
        ]);
    }

    public function lookup(): void
    {
        $phone = (new PaymentService())->normalizePhone($this->request->string('client_phone'));
        $orders = (new Order())->findAllByPhone($phone);

        if ($phone === '' || $orders === []) {
            $this->view('tracking/form', [
                'error' => 'Nenhum pedido foi encontrado para o número informado.',
                'clientPhone' => $this->request->string('client_phone'),
                'contactPhone' => (string) config('app.contact_phone', ''),
                'contactEmail' => (string) config('app.contact_email', ''),
            ]);
            return;
        }

        $this->view('tracking/form', [
            'orders' => $orders,
            'clientPhone' => $phone,
            'contactPhone' => (string) config('app.contact_phone', ''),
            'contactEmail' => (string) config('app.contact_email', ''),
        ]);
    }

    public function status(string $id): void
    {
        $order = (new Order())->withRelations((int) $id);
        if (!$order) {
            $this->response->setStatus(404);
            echo 'Pedido não encontrado.';
            return;
        }
        $this->view('tracking/status', [
            'order' => $order,
            'contactPhone' => (string) config('app.contact_phone', ''),
            'contactEmail' => (string) config('app.contact_email', ''),
        ]);
    }

    public function approve(string $id): void
    {
        try {
            (new OrderService())->approve((int) $id);
            $this->redirect('/pedido/' . $id . '/status');
        } catch (\Throwable $exception) {
            $this->view('tracking/status', [
                'order' => (new Order())->withRelations((int) $id),
                'error' => $exception->getMessage(),
                'contactPhone' => (string) config('app.contact_phone', ''),
                'contactEmail' => (string) config('app.contact_email', ''),
            ]);
        }
    }
}
