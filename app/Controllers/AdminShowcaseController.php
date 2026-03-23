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
        $this->view('admin/showcases/index', [
            'showcases' => (new Showcase())->all('published_at DESC'),
            'eligibleOrders' => (new Order())->approvedForShowcase(),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        (new ShowcaseService())->create([
            'order_id' => (int) $this->request->input('order_id'),
            'before_image' => (string) $this->request->input('before_image'),
            'after_image' => (string) $this->request->input('after_image'),
            'title' => (string) $this->request->input('title'),
            'description' => (string) $this->request->input('description'),
            'sort_order' => (int) $this->request->input('sort_order', 0),
            'is_featured' => $this->request->input('is_featured') ? 1 : 0,
            'is_active' => 1,
            'published_at' => date('Y-m-d H:i:s'),
        ], (int) (Auth::user()['id'] ?? 0));
        $this->redirect('/admin/showcases');
    }

    public function toggle(string $id): void
    {
        (new ShowcaseService())->toggle((int) $id, (int) (Auth::user()['id'] ?? 0));
        $this->redirect('/admin/showcases');
    }
}
