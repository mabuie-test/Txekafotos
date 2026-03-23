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
        $status = (string) $this->request->input('status', '');
        $orders = $status ? \App\Core\Database::instance()->fetchAll('SELECT * FROM orders WHERE status = :status ORDER BY created_at DESC', ['status' => $status]) : (new Order())->all('created_at DESC');
        $this->view('admin/orders/index', ['orders' => $orders, 'status' => $status], 'layouts/admin');
    }

    public function show(string $id): void
    {
        $order = (new Order())->withRelations((int) $id);
        $this->view('admin/orders/show', ['order' => $order], 'layouts/admin');
    }

    public function updateStatus(string $id): void
    {
        (new OrderService())->transitionStatus((int) $id, (string) $this->request->input('status'), (int) (Auth::user()['id'] ?? 0));
        $this->redirect('/admin/pedidos/' . $id);
    }

    public function uploadFinal(string $id): void
    {
        (new OrderService())->uploadFinalImage((int) $id, $this->request->files('edited_image'), (int) (Auth::user()['id'] ?? 0));
        $this->redirect('/admin/pedidos/' . $id);
    }
}
