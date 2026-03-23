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
        $this->view('admin/revisions/index', ['revisions' => (new Revision())->pending()], 'layouts/admin');
    }

    public function respond(string $id): void
    {
        (new RevisionService())->respond((int) $id, (string) $this->request->input('admin_response'), (int) (Auth::user()['id'] ?? 0));
        $this->redirect('/admin/revisoes');
    }
}
