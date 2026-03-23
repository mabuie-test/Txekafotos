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
        $filters = [
            'status' => (string) $this->request->input('status', ''),
            'start_date' => (string) $this->request->input('start_date', ''),
            'end_date' => (string) $this->request->input('end_date', ''),
            'q' => (string) $this->request->input('q', ''),
        ];

        $service = new FinanceService();
        $this->view('admin/finance/index', [
            'revenueTotal' => $service->getTotalRevenue(),
            'revenueToday' => $service->getRevenueToday(),
            'revenueMonth' => $service->getRevenueThisMonth(),
            'pendingTransactions' => $service->getPendingTransactions(),
            'failedTransactions' => $service->getFailedTransactions(),
            'timeline' => $service->getFinancialTimeline($filters['start_date'], $filters['end_date']),
            'transactions' => $service->filteredTransactions($filters),
            'summary' => $service->summary(),
            'financialLogs' => $service->recentFinancialLogs(),
            'filters' => $filters,
        ], 'layouts/admin');
    }

    public function reports(): void
    {
        $filters = [
            'start_date' => (string) $this->request->input('start_date', ''),
            'end_date' => (string) $this->request->input('end_date', ''),
        ];

        $reports = new ReportService();
        $this->view('admin/reports/index', [
            'ordersByStatus' => $reports->ordersByStatus(),
            'ordersByPeriod' => $reports->ordersByPeriod($filters['start_date'], $filters['end_date']),
            'ordersWithRevisions' => $reports->ordersWithRevisions(),
            'approvedWithoutRevision' => $reports->approvedWithoutRevision(),
            'feedbackByPeriod' => $reports->feedbackByPeriod(),
            'satisfactionRate' => $reports->satisfactionRate(),
            'paymentConversionRate' => $reports->paymentConversionRate(),
            'mostRequestedServices' => $reports->mostRequestedServices(),
            'filters' => $filters,
        ], 'layouts/admin');
    }

    public function export(): void
    {
        $service = new FinanceService();
        $status = (string) $this->request->input('status', '');
        $filePath = storage_path('exports/transactions-' . date('Ymd-His') . '.csv');
        $service->exportTransactionsToCsv($filePath, $status !== '' ? $status : null);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        readfile($filePath);
        exit;
    }
}
