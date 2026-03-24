<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\RevisionService;

class RevisionController extends Controller
{
    public function store(string $id): void
    {
        try {
            (new RevisionService())->requestRevision((int) $id, (string) $this->request->input('message'));
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
