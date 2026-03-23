<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Order;
use App\Services\OrderService;

class AdminOrderController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status' => $this->request->string('status'),
            'service_type' => $this->request->string('service_type'),
            'start_date' => $this->request->string('start_date'),
            'end_date' => $this->request->string('end_date'),
            'q' => $this->request->string('q'),
        ];

        $model = new Order();
        $this->view('admin/orders/index', [
            'orders' => $model->filterForAdmin($filters),
            'filters' => $filters,
            'overview' => $model->overview(),
            'serviceBreakdown' => $model->serviceBreakdown(),
        ], 'layouts/admin');
    }

    public function show(string $id): void
    {
        $order = (new Order())->withRelations((int) $id);
        $this->view('admin/orders/show', ['order' => $order], 'layouts/admin');
    }

    public function updateStatus(string $id): void
    {
        try {
            (new OrderService())->transitionStatus((int) $id, $this->request->string('status'), (int) (Auth::user()['id'] ?? 0));
            $this->redirectWithFlash('/admin/pedidos/' . $id, ['success' => 'Status do pedido atualizado com sucesso.']);
        } catch (\Throwable $exception) {
            $this->redirectWithFlash('/admin/pedidos/' . $id, ['error' => $exception->getMessage()]);
        }
    }

    public function uploadFinal(string $id): void
    {
        try {
            (new OrderService())->uploadFinalImage((int) $id, $this->request->files('edited_image'), (int) (Auth::user()['id'] ?? 0));
            $this->redirectWithFlash('/admin/pedidos/' . $id, ['success' => 'Imagem final enviada com sucesso.']);
        } catch (\Throwable $exception) {
            $this->redirectWithFlash('/admin/pedidos/' . $id, ['error' => $exception->getMessage()]);
        }
    }
}
