<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

class ReportService
{
    public function ordersByPeriod(?string $startDate = null, ?string $endDate = null): array
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
        return Database::instance()->fetchAll("SELECT DATE(created_at) as period, COUNT(*) as total FROM orders {$where} GROUP BY DATE(created_at) ORDER BY period ASC", $params);
    }

    public function ordersByStatus(): array
    {
        return Database::instance()->fetchAll('SELECT status, COUNT(*) as total FROM orders GROUP BY status ORDER BY total DESC');
    }

    public function ordersWithRevisions(): array
    {
        return Database::instance()->fetchAll('SELECT orders.id, orders.client_name, COUNT(revisions.id) as revision_count FROM orders INNER JOIN revisions ON revisions.order_id = orders.id GROUP BY orders.id, orders.client_name ORDER BY revision_count DESC');
    }

    public function approvedWithoutRevision(): array
    {
        return Database::instance()->fetchAll("SELECT * FROM orders WHERE status = 'aprovado' AND revisions_used = 0 ORDER BY approved_at DESC");
    }

    public function feedbackByPeriod(): array
    {
        return Database::instance()->fetchAll('SELECT DATE(created_at) as period, COUNT(*) as total, AVG(rating) as average_rating FROM feedbacks GROUP BY DATE(created_at) ORDER BY period ASC');
    }

    public function satisfactionRate(): float
    {
        $row = Database::instance()->fetch('SELECT AVG(rating) as avg_rating FROM feedbacks');
        return round(((float) ($row['avg_rating'] ?? 0) / 5) * 100, 2);
    }

    public function paymentConversionRate(): float
    {
        $totals = Database::instance()->fetch("SELECT (SELECT COUNT(*) FROM orders) as total_orders, (SELECT COUNT(*) FROM orders WHERE status IN ('pago','em_edicao','revisao','concluido','aprovado')) as paid_orders");
        $totalOrders = (int) ($totals['total_orders'] ?? 0);
        return $totalOrders > 0 ? round(((int) $totals['paid_orders'] / $totalOrders) * 100, 2) : 0.0;
    }

    public function mostRequestedServices(): array
    {
        return Database::instance()->fetchAll('SELECT service_type, COUNT(*) as total FROM orders WHERE service_type IS NOT NULL AND service_type != "" GROUP BY service_type ORDER BY total DESC');
    }
}
