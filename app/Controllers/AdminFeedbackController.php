<?php

declare(strict_types=1);

namespace App\Controllers;

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
            'rating' => $this->request->string('rating'),
            'q' => $this->request->string('q'),
        ];

        $model = new Feedback();
        $this->view('admin/feedbacks/index', [
            'feedbacks' => $model->adminList($filters),
            'summary' => $model->summary(),
            'filters' => $filters,
        ], 'layouts/admin');
    }

    public function togglePublication(string $id): void
    {
        (new FeedbackService())->togglePublication((int) $id, (int) (Auth::user()['id'] ?? 0));
        $this->redirectWithFlash('/admin/feedbacks', ['success' => 'Publicação do feedback alterada.']);
    }
}
