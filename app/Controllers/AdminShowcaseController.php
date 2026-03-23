<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
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
            'flash' => App::getInstance()?->session()->getFlash('message'),
            'error' => App::getInstance()?->session()->getFlash('error'),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        try {
            $order = (new Order())->find((int) $this->request->input('order_id'));
            if (!$order || empty($order['primary_image_path']) || empty($order['edited_image_path'])) {
                throw new \RuntimeException('Selecione um pedido elegível com imagens before/after válidas.');
            }

            (new ShowcaseService())->create([
                'order_id' => (int) $order['id'],
                'before_image' => (string) $order['primary_image_path'],
                'after_image' => (string) $order['edited_image_path'],
                'title' => (string) $this->request->input('title'),
                'description' => (string) $this->request->input('description'),
                'sort_order' => (int) $this->request->input('sort_order', 0),
                'is_featured' => $this->request->input('is_featured') ? 1 : 0,
                'is_active' => 1,
                'published_at' => date('Y-m-d H:i:s'),
            ], (int) (Auth::user()['id'] ?? 0));
            App::getInstance()?->session()->flash('message', 'Showcase publicado com sucesso.');
        } catch (\Throwable $exception) {
            App::getInstance()?->session()->flash('error', $exception->getMessage());
        }

        $this->redirect('/admin/showcases');
    }

    public function toggle(string $id): void
    {
        (new ShowcaseService())->toggle((int) $id, (int) (Auth::user()['id'] ?? 0));
        App::getInstance()?->session()->flash('message', 'Status do showcase atualizado.');
        $this->redirect('/admin/showcases');
    }
}
