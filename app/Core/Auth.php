<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Admin;

class Auth
{
    public static function attempt(string $email, string $password): bool
    {
        $adminModel = new Admin();
        $admin = $adminModel->findByEmail($email);

        if (!$admin || !password_verify($password, $admin['password_hash'])) {
            return false;
        }

        App::getInstance()?->session()->regenerate();
        App::getInstance()?->session()->put('admin', [
            'id' => $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
        ]);

        return true;
    }

    public static function user(): ?array
    {
        return App::getInstance()?->session()->get('admin');
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function logout(): void
    {
        App::getInstance()?->session()->forget('admin');
        App::getInstance()?->session()->regenerate();
    }
}
