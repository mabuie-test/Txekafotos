<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Feedback extends Model
{
    protected string $table = 'feedbacks';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO feedbacks (order_id, client_name, rating, message, is_published) VALUES (:order_id, :client_name, :rating, :message, :is_published)', $data);
    }

    public function published(): array
    {
        return $this->db()->fetchAll('SELECT * FROM feedbacks WHERE is_published = 1 ORDER BY created_at DESC LIMIT 12');
    }

    public function average(): float
    {
        $row = $this->db()->fetch('SELECT AVG(rating) as avg_rating FROM feedbacks');
        return round((float) ($row['avg_rating'] ?? 0), 1);
    }

    public function adminList(array $filters = []): array
    {
        $conditions = [];
        $params = [];

        if ($filters['published'] !== '' && $filters['published'] !== null) {
            $conditions[] = 'feedbacks.is_published = :published';
            $params['published'] = (int) $filters['published'];
        }
        if (!empty($filters['rating'])) {
            $conditions[] = 'feedbacks.rating = :rating';
            $params['rating'] = (int) $filters['rating'];
        }
        if (!empty($filters['q'])) {
            $conditions[] = '(feedbacks.client_name LIKE :q OR feedbacks.message LIKE :q OR orders.tracking_code LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        return $this->db()->fetchAll(
            "SELECT feedbacks.*, orders.tracking_code, orders.status as order_status
             FROM feedbacks
             INNER JOIN orders ON orders.id = feedbacks.order_id
             {$where}
             ORDER BY feedbacks.created_at DESC",
            $params
        );
    }

    public function summary(): array
    {
        return $this->db()->fetch(
            'SELECT COUNT(*) as total_feedbacks, SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END) as published_feedbacks, AVG(rating) as average_rating FROM feedbacks'
        ) ?? [];
    }

    public function toggle(int $id): bool
    {
        return $this->db()->execute('UPDATE feedbacks SET is_published = NOT is_published WHERE id = :id', ['id' => $id]);
    }
}
