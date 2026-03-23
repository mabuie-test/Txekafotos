<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO transactions (order_id, debito_reference, payment_method, amount, status, raw_response, last_checked_at) VALUES (:order_id, :debito_reference, :payment_method, :amount, :status, :raw_response, :last_checked_at)', $data);
    }

    public function updateStatusByReference(string $reference, string $status, array $rawResponse): bool
    {
        return $this->db()->execute('UPDATE transactions SET status = :status, raw_response = :raw_response, last_checked_at = NOW() WHERE debito_reference = :reference', [
            'status' => $status,
            'raw_response' => json_encode($rawResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'reference' => $reference,
        ]);
    }

    public function pending(): array
    {
        return $this->db()->fetchAll("SELECT * FROM transactions WHERE status IN ('pending', 'processing') ORDER BY created_at ASC");
    }

    public function latest(int $limit = 10): array
    {
        return $this->db()->fetchAll('SELECT * FROM transactions ORDER BY created_at DESC LIMIT ' . (int) $limit);
    }

    public function findByReference(string $reference): ?array
    {
        return $this->db()->fetch('SELECT * FROM transactions WHERE debito_reference = :reference LIMIT 1', ['reference' => $reference]);
    }
}
