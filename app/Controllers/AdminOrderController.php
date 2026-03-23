<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\Order;
use App\Services\OrderService;

class AdminOrderController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status' => (string) $this->request->input('status', ''),
            'service_type' => (string) $this->request->input('service_type', ''),
            'start_date' => (string) $this->request->input('start_date', ''),
            'end_date' => (string) $this->request->input('end_date', ''),
            'q' => (string) $this->request->input('q', ''),
        ];

        $model = new Order();
        $this->view('admin/orders/index', [
            'orders' => $model->filterForAdmin($filters),
            'filters' => $filters,
            'overview' => $model->overview(),
            'serviceBreakdown' => $model->serviceBreakdown(),
            'flash' => App::getInstance()?->session()->getFlash('message'),
            'error' => App::getInstance()?->session()->getFlash('error'),
        ], 'layouts/admin');
    }

    public function show(string $id): void
    {
        $order = (new Order())->withRelations((int) $id);
        $this->view('admin/orders/show', [
            'order' => $order,
            'flash' => App::getInstance()?->session()->getFlash('message'),
            'error' => App::getInstance()?->session()->getFlash('error'),
        ], 'layouts/admin');
    }

    public function updateStatus(string $id): void
    {
        try {
            (new OrderService())->transitionStatus((int) $id, (string) $this->request->input('status'), (int) (Auth::user()['id'] ?? 0));
            App::getInstance()?->session()->flash('message', 'Status do pedido atualizado com sucesso.');
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/pedidos/' . $id);
    }

    public function uploadFinal(string $id): void
    {
        try {
            (new OrderService())->uploadFinalImage((int) $id, $this->request->files('edited_image'), (int) (Auth::user()['id'] ?? 0));
            App::getInstance()?->session()->flash('message', 'Imagem final enviada com sucesso.');
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/pedidos/' . $id);
    }
}
