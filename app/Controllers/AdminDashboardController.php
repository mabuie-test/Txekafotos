<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ActivityLog;
use App\Models\Feedback;
use App\Models\Order;
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

        $this->view('admin/dashboard/index', [
            'statusStats' => $orders->getDashboardStats(),
            'latestOrders' => $orders->latest(),
            'latestTransactions' => (new Transaction())->latest(),
            'activityLogs' => (new ActivityLog())->latest(10),
            'revenueTotal' => $finance->getTotalRevenue(),
            'revenueToday' => $finance->getRevenueToday(),
            'revenueMonth' => $finance->getRevenueThisMonth(),
            'averageRating' => $feedback->average(),
            'showcaseCount' => count((new Showcase())->active()),
        ], 'layouts/admin');
    }
}
