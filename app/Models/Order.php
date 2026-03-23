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

    public function updateStatus(int $id, string $status): bool
    {
        return $this->db()->execute('UPDATE orders SET status = :status WHERE id = :id', compact('status', 'id'));
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

    public function withRelations(int $id): ?array
    {
        $order = $this->find($id);
        if (!$order) {
            return null;
        }

        $order['images'] = $this->db()->fetchAll('SELECT * FROM order_images WHERE order_id = :id ORDER BY sort_order ASC, id ASC', ['id' => $id]);
        $order['transaction'] = $this->db()->fetch('SELECT * FROM transactions WHERE order_id = :id ORDER BY id DESC LIMIT 1', ['id' => $id]);
        $order['revisions'] = $this->db()->fetchAll('SELECT * FROM revisions WHERE order_id = :id ORDER BY created_at DESC', ['id' => $id]);
        $order['feedback'] = $this->db()->fetch('SELECT * FROM feedbacks WHERE order_id = :id LIMIT 1', ['id' => $id]);
        return $order;
    }

    public function approvedForShowcase(): array
    {
        return $this->db()->fetchAll("SELECT * FROM orders WHERE status IN ('concluido', 'aprovado') AND edited_image_path IS NOT NULL ORDER BY updated_at DESC");
    }
}
