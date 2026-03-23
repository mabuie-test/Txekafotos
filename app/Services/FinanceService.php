<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\FinancialLog;
use App\Models\Transaction;

class FinanceService
{
    public function __construct(
        private readonly FinancialLog $logs = new FinancialLog(),
        private readonly Transaction $transactions = new Transaction(),
    ) {
    }

    public function registerIncome(int $orderId, int $transactionId, float $amount, string $description, ?int $adminId = null): int
    {
        return $this->logs->create([
            'type' => 'entrada',
            'amount' => $amount,
            'description' => $description,
            'related_order_id' => $orderId,
            'related_transaction_id' => $transactionId,
            'created_by_admin_id' => $adminId,
        ]);
    }

    public function registerAdjustment(float $amount, string $description, ?int $adminId = null, ?int $orderId = null, ?int $transactionId = null): int
    {
        return $this->logs->create([
            'type' => 'ajuste',
            'amount' => $amount,
            'description' => $description,
            'related_order_id' => $orderId,
            'related_transaction_id' => $transactionId,
            'created_by_admin_id' => $adminId,
        ]);
    }

    public function getTotalRevenue(): float
    {
        $row = Database::instance()->fetch("SELECT COALESCE(SUM(amount),0) as total FROM financial_logs WHERE type = 'entrada'");
        return (float) ($row['total'] ?? 0);
    }

    public function getRevenueToday(): float
    {
        $row = Database::instance()->fetch("SELECT COALESCE(SUM(amount),0) as total FROM financial_logs WHERE type = 'entrada' AND DATE(created_at) = CURDATE()");
        return (float) ($row['total'] ?? 0);
    }

    public function getRevenueThisMonth(): float
    {
        $row = Database::instance()->fetch("SELECT COALESCE(SUM(amount),0) as total FROM financial_logs WHERE type = 'entrada' AND YEAR(created_at) = YEAR(CURDATE()) AND MONTH(created_at) = MONTH(CURDATE())");
        return (float) ($row['total'] ?? 0);
    }

    public function getPendingTransactions(): array
    {
        return Database::instance()->fetchAll("SELECT * FROM transactions WHERE status IN ('pending','processing') ORDER BY created_at ASC");
    }

    public function getFailedTransactions(): array
    {
        return Database::instance()->fetchAll("SELECT * FROM transactions WHERE status = 'failed' ORDER BY updated_at DESC");
    }

    public function getFinancialTimeline(?string $startDate = null, ?string $endDate = null): array
    {
        $conditions = [];
        $params = [];
        if ($startDate) {
            $conditions[] = 'DATE(created_at) >= :start_date';
            $params['start_date'] = $startDate;
        }
        if ($endDate) {
            $conditions[] = 'DATE(created_at) <= :end_date';
            $params['end_date'] = $endDate;
        }
        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        return Database::instance()->fetchAll("SELECT DATE(created_at) as period, SUM(amount) as total FROM financial_logs {$where} GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC", $params);
    }

    public function exportTransactionsToCsv(string $filePath, ?string $status = null): string
    {
        $sql = 'SELECT * FROM transactions';
        $params = [];
        if ($status) {
            $sql .= ' WHERE status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY created_at DESC';
        $rows = Database::instance()->fetchAll($sql, $params);
        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['ID', 'Order ID', 'Reference', 'Method', 'Amount', 'Status', 'Created At']);
        foreach ($rows as $row) {
            fputcsv($handle, [$row['id'], $row['order_id'], $row['debito_reference'], $row['payment_method'], $row['amount'], $row['status'], $row['created_at']]);
        }
        fclose($handle);
        return $filePath;
    }
}
