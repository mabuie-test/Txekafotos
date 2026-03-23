<?php

declare(strict_types=1);

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        $base = defined('BASE_PATH') ? BASE_PATH : dirname(__DIR__, 2);
        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : $base;
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        return App\Core\App::getInstance()?->config($key, $default) ?? $default;
    }
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return rtrim((string) config('app.url', ''), '/') . '/assets/' . ltrim($path, '/');
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        return rtrim((string) config('app.url', ''), '/') . '/' . ltrim($path, '/');
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (!function_exists('tap')) {
    function tap(mixed $value, callable $callback): mixed
    {
        $callback($value);
        return $value;
    }
}

if (!function_exists('media_url')) {
    function media_url(string $path): string
    {
        return '/media?path=' . rawurlencode($path);
    }
}

if (!function_exists('session_instance')) {
    function session_instance(): ?App\Core\Session
    {
        return App\Core\App::getInstance()?->session();
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        $old = session_instance()?->peekFlash('old', []);
        return $old[$key] ?? $default;
    }
}

if (!function_exists('flash_message')) {
    function flash_message(string $key, mixed $default = null): mixed
    {
        return session_instance()?->getFlash($key, $default);
    }
}

if (!function_exists('money')) {
    function money(float|int|string|null $amount, string $currency = 'MZN'): string
    {
        return number_format((float) $amount, 2) . ' ' . $currency;
    }
}

if (!function_exists('status_badge_class')) {
    function status_badge_class(string $status): string
    {
        return match ($status) {
            'aprovado', 'completed', 'success' => 'text-bg-success',
            'concluido', 'pago', 'em_edicao' => 'text-bg-primary',
            'revisao', 'pagamento_em_analise', 'processing', 'pending' => 'text-bg-warning',
            'falhou_pagamento', 'failed', 'cancelado', 'cancelled' => 'text-bg-danger',
            default => 'text-bg-secondary',
        };
    }
}
