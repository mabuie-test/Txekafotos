<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\FinanceService;
use App\Services\ReportService;

class AdminFinanceController extends Controller
{
    public function index(): void
    {
        $service = new FinanceService();
        $this->view('admin/finance/index', [
            'revenueTotal' => $service->getTotalRevenue(),
            'revenueToday' => $service->getRevenueToday(),
            'revenueMonth' => $service->getRevenueThisMonth(),
            'pendingTransactions' => $service->getPendingTransactions(),
            'failedTransactions' => $service->getFailedTransactions(),
            'timeline' => $service->getFinancialTimeline($this->request->input('start_date'), $this->request->input('end_date')),
        ], 'layouts/admin');
    }

    public function reports(): void
    {
        $reports = new ReportService();
        $this->view('admin/reports/index', [
            'ordersByStatus' => $reports->ordersByStatus(),
            'ordersByPeriod' => $reports->ordersByPeriod($this->request->input('start_date'), $this->request->input('end_date')),
            'ordersWithRevisions' => $reports->ordersWithRevisions(),
            'approvedWithoutRevision' => $reports->approvedWithoutRevision(),
            'feedbackByPeriod' => $reports->feedbackByPeriod(),
            'satisfactionRate' => $reports->satisfactionRate(),
            'paymentConversionRate' => $reports->paymentConversionRate(),
            'mostRequestedServices' => $reports->mostRequestedServices(),
        ], 'layouts/admin');
    }
}
