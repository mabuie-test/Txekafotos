<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Order;
use App\Models\Showcase;
use App\Services\ShowcaseService;

class AdminShowcaseController extends Controller
{
    public function index(): void
    {
        $showcaseModel = new Showcase();
        $this->view('admin/showcases/index', [
            'showcases' => $showcaseModel->adminList(),
            'showcaseSummary' => $showcaseModel->summary(),
            'eligibleOrders' => (new Order())->approvedForShowcase(),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        try {
            $order = (new Order())->find($this->request->integer('order_id'));
            if (!$order || empty($order['primary_image_path']) || empty($order['edited_image_path'])) {
                throw new \RuntimeException('Selecione um pedido elegível com imagens before/after válidas.');
            }

            (new ShowcaseService())->create([
                'order_id' => (int) $order['id'],
                'before_image' => (string) $order['primary_image_path'],
                'after_image' => (string) $order['edited_image_path'],
                'title' => $this->request->string('title'),
                'description' => $this->request->string('description'),
                'sort_order' => $this->request->integer('sort_order', 0),
                'is_featured' => $this->request->boolean('is_featured') ? 1 : 0,
                'is_active' => 1,
                'published_at' => date('Y-m-d H:i:s'),
            ], (int) (Auth::user()['id'] ?? 0));
            $this->redirectWithFlash('/admin/showcases', ['success' => 'Showcase publicado com sucesso.']);
        } catch (\Throwable $exception) {
            $this->redirectWithFlash('/admin/showcases', ['error' => $exception->getMessage()]);
        }
    }

    public function toggle(string $id): void
    {
        (new ShowcaseService())->toggle((int) $id, (int) (Auth::user()['id'] ?? 0));
        $this->redirectWithFlash('/admin/showcases', ['success' => 'Status do showcase atualizado.']);
    }
}
