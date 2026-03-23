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
