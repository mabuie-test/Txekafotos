<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ActivityLog extends Model
{
    protected string $table = 'activity_logs';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO activity_logs (actor_type, actor_id, action, entity_type, entity_id, description, metadata) VALUES (:actor_type, :actor_id, :action, :entity_type, :entity_id, :description, :metadata)', $data);
    }

    public function latest(int $limit = 25): array
    {
        return $this->db()->fetchAll('SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT ' . (int) $limit);
    }
}
