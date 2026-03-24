<?php

declare(strict_types=1);

namespace App\Core;

class Response
{
    public function redirect(string $path, int $status = 302): void
    {
        http_response_code($status);
        header('Location: ' . $path);
        exit;
    }

    public function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function setStatus(int $status): void
    {
        http_response_code($status);
    }
}
