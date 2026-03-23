<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class OrderImage extends Model
{
    protected string $table = 'order_images';

    public function create(array $data): int
    {
        return $this->db()->insert('INSERT INTO order_images (order_id, image_type, file_path, original_name, mime_type, file_size, sort_order) VALUES (:order_id, :image_type, :file_path, :original_name, :mime_type, :file_size, :sort_order)', $data);
    }

    public function forOrder(int $orderId, string $type = 'extra'): array
    {
        return $this->db()->fetchAll('SELECT * FROM order_images WHERE order_id = :order_id AND image_type = :image_type ORDER BY sort_order ASC, id ASC', ['order_id' => $orderId, 'image_type' => $type]);
    }
}
