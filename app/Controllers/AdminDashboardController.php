<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Order;
use App\Models\Revision;
use App\Models\Showcase;
use App\Models\Transaction;
use App\Services\FinanceService;

class AdminDashboardController extends Controller
{
    public function index(): void
    {
        $orders = new Order();
        $feedback = new Feedback();
        $finance = new FinanceService();
        $revision = new Revision();
        $showcase = new Showcase();
        $overview = $orders->overview();

        $this->view('admin/dashboard/index', [
            'overview' => $overview,
            'statusStats' => $orders->getDashboardStats(),
            'serviceBreakdown' => $orders->serviceBreakdown(),
            'latestOrders' => $orders->latest(),
            'latestTransactions' => (new Transaction())->latest(),
            'activityLogs' => (new ActivityLog())->latest(10),
            'revenueTotal' => $finance->getTotalRevenue(),
            'revenueToday' => $finance->getRevenueToday(),
            'revenueMonth' => $finance->getRevenueThisMonth(),
            'averageRating' => $feedback->average(),
            'feedbackSummary' => $feedback->summary(),
            'revisionSummary' => $revision->summary(),
            'showcaseSummary' => $showcase->summary(),
            'financeSummary' => $finance->summary(),
        ], 'layouts/admin');
    }
}
