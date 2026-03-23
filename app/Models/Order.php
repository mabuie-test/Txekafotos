<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    public const STATUSES = [
        'pendente_pagamento',
        'pagamento_em_analise',
        'pago',
        'em_edicao',
        'revisao',
        'concluido',
        'aprovado',
        'cancelado',
        'falhou_pagamento',
    ];

    protected string $table = 'orders';

    public function create(array $data): int
    {
        return $this->db()->insert(
            'INSERT INTO orders (tracking_code, tracking_token, client_name, client_phone, service_type, description, primary_image_path, amount, status, terms_accepted, revisions_used, internal_notes) VALUES (:tracking_code, :tracking_token, :client_name, :client_phone, :service_type, :description, :primary_image_path, :amount, :status, :terms_accepted, :revisions_used, :internal_notes)',
            $data
        );
    }

    public function existsByTrackingCode(string $trackingCode): bool
    {
        return $this->db()->fetch('SELECT id FROM orders WHERE tracking_code = :tracking_code LIMIT 1', ['tracking_code' => $trackingCode]) !== null;
    }

    public function updateStatus(int $id, string $status): bool
    {
        return $this->db()->execute('UPDATE orders SET status = :status WHERE id = :id', compact('status', 'id'));
    }

    public function markPaymentUnderReview(int $id): bool
    {
        return $this->db()->execute("UPDATE orders SET status = 'pagamento_em_analise' WHERE id = :id", ['id' => $id]);
    }

    public function markPaymentConfirmed(int $id): bool
    {
        return $this->db()->execute("UPDATE orders SET status = 'pago', payment_confirmed_at = NOW() WHERE id = :id", ['id' => $id]);
    }

    public function markPaymentFailed(int $id): bool
    {
        return $this->db()->execute("UPDATE orders SET status = 'falhou_pagamento' WHERE id = :id", ['id' => $id]);
    }

    public function updateEditedImage(int $id, string $path): bool
    {
        return $this->db()->execute('UPDATE orders SET edited_image_path = :path, status = :status WHERE id = :id', ['path' => $path, 'status' => 'concluido', 'id' => $id]);
    }

    public function findByTracking(int $id, string $phone): ?array
    {
        return $this->db()->fetch('SELECT * FROM orders WHERE id = :id AND client_phone = :phone LIMIT 1', ['id' => $id, 'phone' => $phone]);
    }

    public function getDashboardStats(): array
    {
        return $this->db()->fetchAll('SELECT status, COUNT(*) as total FROM orders GROUP BY status');
    }

    public function latest(int $limit = 10): array
    {
        return $this->db()->fetchAll('SELECT * FROM orders ORDER BY created_at DESC LIMIT ' . (int) $limit);
    }

    public function filterForAdmin(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['service_type'])) {
            $conditions[] = 'service_type = :service_type';
            $params['service_type'] = $filters['service_type'];
        }

        if (!empty($filters['start_date'])) {
            $conditions[] = 'DATE(created_at) >= :start_date';
            $params['start_date'] = $filters['start_date'];
        }

        if (!empty($filters['end_date'])) {
            $conditions[] = 'DATE(created_at) <= :end_date';
            $params['end_date'] = $filters['end_date'];
        }

        if (!empty($filters['q'])) {
            $conditions[] = '(client_name LIKE :q OR client_phone LIKE :q OR tracking_code LIKE :q OR description LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return $this->db()->fetchAll("SELECT * FROM orders {$where} ORDER BY created_at DESC", $params);
    }

    public function overview(): array
    {
        return $this->db()->fetch(
            "SELECT
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pendente_pagamento' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'pago' THEN 1 ELSE 0 END) as paid_orders,
                SUM(CASE WHEN status = 'em_edicao' THEN 1 ELSE 0 END) as editing_orders,
                SUM(CASE WHEN status = 'revisao' THEN 1 ELSE 0 END) as revision_orders,
                SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as completed_orders,
                SUM(CASE WHEN status = 'aprovado' THEN 1 ELSE 0 END) as approved_orders,
                SUM(CASE WHEN revisions_used = 0 THEN 1 ELSE 0 END) as without_revision,
                SUM(CASE WHEN revisions_used > 0 THEN 1 ELSE 0 END) as with_revision,
                AVG(amount) as average_ticket
            FROM orders"
        ) ?? [];
    }

    public function withRelations(int $id): ?array
    {
        $order = $this->find($id);
        if (!$order) {
            return null;
        }

        $order['images'] = $this->db()->fetchAll('SELECT * FROM order_images WHERE order_id = :id ORDER BY sort_order ASC, id ASC', ['id' => $id]);
        $order['extra_images'] = $this->db()->fetchAll("SELECT * FROM order_images WHERE order_id = :id AND image_type = 'extra' ORDER BY sort_order ASC, id ASC", ['id' => $id]);
        $order['transaction'] = $this->db()->fetch('SELECT * FROM transactions WHERE order_id = :id ORDER BY id DESC LIMIT 1', ['id' => $id]);
        $order['transactions'] = $this->db()->fetchAll('SELECT * FROM transactions WHERE order_id = :id ORDER BY created_at DESC', ['id' => $id]);
        $order['revisions'] = $this->db()->fetchAll('SELECT * FROM revisions WHERE order_id = :id ORDER BY created_at DESC', ['id' => $id]);
        $order['feedback'] = $this->db()->fetch('SELECT * FROM feedbacks WHERE order_id = :id LIMIT 1', ['id' => $id]);
        $order['activities'] = $this->db()->fetchAll("SELECT * FROM activity_logs WHERE entity_type = 'order' AND entity_id = :id ORDER BY created_at DESC", ['id' => $id]);
        return $order;
    }

    public function approvedForShowcase(): array
    {
        return $this->db()->fetchAll("SELECT * FROM orders WHERE status IN ('concluido', 'aprovado') AND edited_image_path IS NOT NULL ORDER BY updated_at DESC");
    }

    public function serviceBreakdown(): array
    {
        return $this->db()->fetchAll('SELECT COALESCE(service_type, "personalizado") as service_type, COUNT(*) as total FROM orders GROUP BY COALESCE(service_type, "personalizado") ORDER BY total DESC');
    }
}
