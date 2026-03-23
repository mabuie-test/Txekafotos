<?php

declare(strict_types=1);

namespace App\Core;

class Csrf
{
    public static function token(): string
    {
        $token = App::getInstance()?->session()->get('_csrf_token');
        if (!$token) {
            $token = bin2hex(random_bytes(32));
            App::getInstance()?->session()->put('_csrf_token', $token);
        }
        return $token;
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_token" value="' . e(self::token()) . '">';
    }

    public static function validate(?string $token): bool
    {
        return hash_equals((string) App::getInstance()?->session()->get('_csrf_token'), (string) $token);
    }
}
