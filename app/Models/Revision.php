<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Revision extends Model
{
    protected string $table = 'revisions';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO revisions (order_id, client_message, admin_response, status) VALUES (:order_id, :client_message, :admin_response, :status)', $data);
    }

    public function pending(): array
    {
        return $this->db()->fetchAll("SELECT revisions.*, orders.client_name, orders.status as order_status, orders.tracking_code FROM revisions INNER JOIN orders ON orders.id = revisions.order_id WHERE revisions.status = 'pending' ORDER BY revisions.created_at DESC");
    }

    public function adminList(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if (!empty($filters['status'])) {
            $conditions[] = 'revisions.status = :status';
            $params['status'] = $filters['status'];
        }
        if (!empty($filters['q'])) {
            $conditions[] = '(orders.client_name LIKE :q OR orders.tracking_code LIKE :q OR revisions.client_message LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return $this->db()->fetchAll(
            "SELECT revisions.*, orders.client_name, orders.status as order_status, orders.tracking_code
             FROM revisions
             INNER JOIN orders ON orders.id = revisions.order_id
             {$where}
             ORDER BY revisions.created_at DESC",
            $params
        );
    }

    public function summary(): array
    {
        return $this->db()->fetch(
            "SELECT COUNT(*) as total_revisions,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_revisions,
                    SUM(CASE WHEN status = 'answered' THEN 1 ELSE 0 END) as answered_revisions,
                    SUM(CASE WHEN status = 'resolved' THEN 1 ELSE 0 END) as resolved_revisions
             FROM revisions"
        ) ?? [];
    }

    public function respond(int $id, string $response): bool
    {
        return $this->db()->execute("UPDATE revisions SET admin_response = :response, status = 'answered', updated_at = NOW() WHERE id = :id", ['response' => $response, 'id' => $id]);
    }
}
