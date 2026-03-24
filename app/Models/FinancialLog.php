<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class FinancialLog extends Model
{
    protected string $table = 'financial_logs';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO financial_logs (type, amount, description, related_order_id, related_transaction_id, created_by_admin_id) VALUES (:type, :amount, :description, :related_order_id, :related_transaction_id, :created_by_admin_id)', $data);
    }
}
