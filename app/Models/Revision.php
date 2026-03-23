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
        return $this->db()->fetchAll("SELECT revisions.*, orders.client_name, orders.status as order_status FROM revisions INNER JOIN orders ON orders.id = revisions.order_id WHERE revisions.status = 'pending' ORDER BY revisions.created_at DESC");
    }

    public function respond(int $id, string $response): bool
    {
        return $this->db()->execute("UPDATE revisions SET admin_response = :response, status = 'answered', updated_at = NOW() WHERE id = :id", ['response' => $response, 'id' => $id]);
    }
}
