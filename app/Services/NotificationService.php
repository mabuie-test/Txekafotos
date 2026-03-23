<?php

declare(strict_types=1);

namespace App\Services;

class NotificationService
{
    public function buildInternalNotification(string $title, string $message, array $context = []): array
    {
        return [
            'title' => $title,
            'message' => $message,
            'context' => $context,
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }
}
