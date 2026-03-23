<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\App;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\Feedback;
use App\Services\FeedbackService;

class AdminFeedbackController extends Controller
{
    public function index(): void
    {
        $filters = [
            'published' => $this->request->input('published', ''),
            'rating' => (string) $this->request->input('rating', ''),
            'q' => (string) $this->request->input('q', ''),
        ];

        $model = new Feedback();
        $this->view('admin/feedbacks/index', [
            'feedbacks' => $model->adminList($filters),
            'summary' => $model->summary(),
            'filters' => $filters,
            'flash' => App::getInstance()?->session()->getFlash('message'),
        ], 'layouts/admin');
    }

    public function togglePublication(string $id): void
    {
        (new FeedbackService())->togglePublication((int) $id, (int) (Auth::user()['id'] ?? 0));
        App::getInstance()?->session()->flash('message', 'Publicação do feedback alterada.');
        $this->redirect('/admin/feedbacks');
    }
}
