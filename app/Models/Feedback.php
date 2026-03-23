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

    public function toggle(int $id): bool
    {
        return $this->db()->execute('UPDATE feedbacks SET is_published = NOT is_published WHERE id = :id', ['id' => $id]);
    }
}
