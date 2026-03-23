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
        return Database::instance()->fetchAll(
            "SELECT transactions.*, orders.client_name, orders.tracking_code
             FROM transactions
             INNER JOIN orders ON orders.id = transactions.order_id
             WHERE transactions.status IN ('pending','processing')
             ORDER BY transactions.created_at ASC"
        );
    }

    public function getFailedTransactions(): array
    {
        return Database::instance()->fetchAll(
            "SELECT transactions.*, orders.client_name, orders.tracking_code
             FROM transactions
             INNER JOIN orders ON orders.id = transactions.order_id
             WHERE transactions.status = 'failed'
             ORDER BY transactions.updated_at DESC"
        );
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

    public function summary(): array
    {
        return Database::instance()->fetch(
            "SELECT
                (SELECT COUNT(*) FROM transactions WHERE status = 'completed') as completed_transactions,
                (SELECT COUNT(*) FROM transactions WHERE status IN ('pending','processing')) as pending_transactions,
                (SELECT COUNT(*) FROM transactions WHERE status = 'failed') as failed_transactions,
                (SELECT COUNT(*) FROM orders) as total_orders,
                (SELECT COUNT(*) FROM orders WHERE status IN ('pago','em_edicao','revisao','concluido','aprovado')) as paid_orders,
                (SELECT AVG(amount) FROM transactions WHERE status = 'completed') as average_ticket"
        ) ?? [];
    }

    public function filteredTransactions(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'transactions.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['start_date'])) {
            $conditions[] = 'DATE(transactions.created_at) >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }
        if (!empty($filters['end_date'])) {
            $conditions[] = 'DATE(transactions.created_at) <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }
        if (!empty($filters['q'])) {
            $conditions[] = '(transactions.debito_reference LIKE :q OR orders.client_name LIKE :q OR orders.tracking_code LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return Database::instance()->fetchAll(
            "SELECT transactions.*, orders.client_name, orders.tracking_code
             FROM transactions
             INNER JOIN orders ON orders.id = transactions.order_id
             {$where}
             ORDER BY transactions.created_at DESC",
            $params
        );
    }

    public function recentFinancialLogs(int $limit = 15): array
    {
        return Database::instance()->fetchAll(
            'SELECT * FROM financial_logs ORDER BY created_at DESC LIMIT ' . (int) $limit
        );
    }

    public function exportTransactionsToCsv(string $filePath, ?string $status = null): string
    {
        $sql = 'SELECT transactions.*, orders.client_name, orders.tracking_code FROM transactions INNER JOIN orders ON orders.id = transactions.order_id';
        $params = [];
        if ($status) {
            $sql .= ' WHERE transactions.status = :status';
            $params['status'] = $status;
        }
        $sql .= ' ORDER BY transactions.created_at DESC';
        $rows = Database::instance()->fetchAll($sql, $params);
        $handle = fopen($filePath, 'w');
        fputcsv($handle, ['ID', 'Pedido', 'Tracking', 'Cliente', 'Reference', 'Method', 'Amount', 'Status', 'Gateway', 'Created At']);
        foreach ($rows as $row) {
            fputcsv($handle, [$row['id'], $row['order_id'], $row['tracking_code'], $row['client_name'], $row['debito_reference'], $row['payment_method'], $row['amount'], $row['status'], $row['gateway_status'], $row['created_at']]);
        }
        fclose($handle);
        return $filePath;
    }
}
