<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Admin extends Model
{
    protected string $table = 'admins';

    public function findByEmail(string $email): ?array
    {
        return $this->db()->fetch('SELECT * FROM admins WHERE email = :email LIMIT 1', ['email' => $email]);
    }
}
