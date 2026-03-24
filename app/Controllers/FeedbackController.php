<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\FeedbackService;

class FeedbackController extends Controller
{
    public function store(string $id): void
    {
        try {
            (new FeedbackService())->create([
                'order_id' => (int) $id,
                'client_name' => (string) $this->request->input('client_name'),
                'rating' => (int) $this->request->input('rating'),
                'message' => (string) $this->request->input('message'),
                'is_published' => 0,
            ]);
            $this->redirect('/pedido/' . $id . '/status');
        } catch (\Throwable $exception) {
            $this->view('tracking/status', [
                'order' => (new \App\Models\Order())->withRelations((int) $id),
                'error' => $exception->getMessage(),
                'contactPhone' => (string) config('app.contact_phone', ''),
                'contactEmail' => (string) config('app.contact_email', ''),
            ]);
        }
    }
}
