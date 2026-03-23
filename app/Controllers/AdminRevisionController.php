<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\Revision;
use App\Services\RevisionService;

class AdminRevisionController extends Controller
{
    public function index(): void
    {
        $filters = [
            'status' => (string) $this->request->input('status', ''),
            'q' => (string) $this->request->input('q', ''),
        ];

        $model = new Revision();
        $this->view('admin/revisions/index', [
            'revisions' => $model->adminList($filters),
            'summary' => $model->summary(),
            'filters' => $filters,
            'flash' => App::getInstance()?->session()->getFlash('message'),
        ], 'layouts/admin');
    }

    public function respond(string $id): void
    {
        (new RevisionService())->respond((int) $id, (string) $this->request->input('admin_response'), (int) (Auth::user()['id'] ?? 0));
        App::getInstance()?->session()->flash('message', 'Resposta enviada e pedido devolvido para edição.');
        $this->redirect('/admin/revisoes');
    }
}
