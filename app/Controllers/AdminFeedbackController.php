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
        $this->view('admin/feedbacks/index', ['feedbacks' => (new Feedback())->all('created_at DESC')], 'layouts/admin');
    }

    public function togglePublication(string $id): void
    {
        (new FeedbackService())->togglePublication((int) $id, (int) (Auth::user()['id'] ?? 0));
        $this->redirect('/admin/feedbacks');
    }
}
