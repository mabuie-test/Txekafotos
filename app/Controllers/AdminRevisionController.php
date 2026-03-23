<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Revision;
use App\Services\RevisionService;

class AdminRevisionController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status' => $this->request->string('status'),
            'q' => $this->request->string('q'),
        ];

        $model = new Revision();
        $this->view('admin/revisions/index', [
            'revisions' => $model->adminList($filters),
            'summary' => $model->summary(),
            'filters' => $filters,
        ], 'layouts/admin');
    }

    public function respond(string $id): void
    {
        (new RevisionService())->respond((int) $id, $this->request->string('admin_response'), (int) (Auth::user()['id'] ?? 0));
        $this->redirectWithFlash('/admin/revisoes', ['success' => 'Resposta enviada e pedido devolvido para edição.']);
    }
}
