<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Showcase extends Model
{
    protected string $table = 'showcases';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO showcases (order_id, before_image, after_image, title, description, sort_order, is_featured, is_active, published_at) VALUES (:order_id, :before_image, :after_image, :title, :description, :sort_order, :is_featured, :is_active, :published_at)', $data);
    }

    public function active(): array
    {
        return $this->db()->fetchAll('SELECT * FROM showcases WHERE is_active = 1 ORDER BY is_featured DESC, sort_order ASC, published_at DESC');
    }

    public function adminList(): array
    {
        return $this->db()->fetchAll(
            "SELECT showcases.*, orders.client_name, orders.tracking_code
             FROM showcases
             INNER JOIN orders ON orders.id = showcases.order_id
             ORDER BY showcases.published_at DESC, showcases.created_at DESC"
        );
    }

    public function summary(): array
    {
        return $this->db()->fetch(
            'SELECT COUNT(*) as total_showcases, SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_showcases, SUM(CASE WHEN is_featured = 1 THEN 1 ELSE 0 END) as featured_showcases FROM showcases'
        ) ?? [];
    }

    public function toggle(int $id): bool
    {
        return $this->db()->execute('UPDATE showcases SET is_active = NOT is_active WHERE id = :id', ['id' => $id]);
    }
}
