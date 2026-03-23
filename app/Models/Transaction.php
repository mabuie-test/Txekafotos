<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Transaction extends Model
{
    protected string $table = 'transactions';

    public function create(array $data): int
    {
        return $this->db()->insert(
            'INSERT INTO transactions (order_id, debito_reference, payment_method, amount, status, msisdn, reference_description, gateway_status, failure_reason, raw_response, last_checked_at) VALUES (:order_id, :debito_reference, :payment_method, :amount, :status, :msisdn, :reference_description, :gateway_status, :failure_reason, :raw_response, :last_checked_at)',
            $data
        );
    }

    public function updateGatewayState(int $id, string $status, string $gatewayStatus, array $rawResponse, ?string $failureReason = null): bool
    {
        return $this->db()->execute(
            'UPDATE transactions SET status = :status, gateway_status = :gateway_status, failure_reason = :failure_reason, raw_response = :raw_response, last_checked_at = NOW() WHERE id = :id',
            [
                'id' => $id,
                'status' => $status,
                'gateway_status' => $gatewayStatus,
                'failure_reason' => $failureReason,
                'raw_response' => json_encode($rawResponse, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ]
        );
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

    public function latestByOrder(int $orderId): ?array
    {
        return $this->db()->fetch('SELECT * FROM transactions WHERE order_id = :order_id ORDER BY created_at DESC LIMIT 1', ['order_id' => $orderId]);
    }

    public function activeByOrder(int $orderId): ?array
    {
        return $this->db()->fetch("SELECT * FROM transactions WHERE order_id = :order_id AND status IN ('pending', 'processing') ORDER BY created_at DESC LIMIT 1", ['order_id' => $orderId]);
    }

    public function completedByOrder(int $orderId): ?array
    {
        return $this->db()->fetch("SELECT * FROM transactions WHERE order_id = :order_id AND status = 'completed' ORDER BY created_at DESC LIMIT 1", ['order_id' => $orderId]);
    }
}
