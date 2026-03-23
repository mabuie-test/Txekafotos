<?php

declare(strict_types=1);

namespace App\Core;

abstract class Model
{
    protected string $table;

    protected function db(): Database
    {
        return Database::instance();
    }

    public function find(int $id): ?array
    {
        return $this->db()->fetch("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", ['id' => $id]);
    }

    public function all(string $orderBy = 'id DESC'): array
    {
        return $this->db()->fetchAll("SELECT * FROM {$this->table} ORDER BY {$orderBy}");
    }
}
