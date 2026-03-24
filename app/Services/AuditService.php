<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\ActivityLog;

class AuditService
{
    public function __construct(private readonly ActivityLog $logs = new ActivityLog())
    {
    }

    public function log(string $actorType, ?int $actorId, string $action, string $entityType, int $entityId, string $description, ?array $metadata = null): int
    {
        return $this->logs->create([
            'actor_type' => $actorType,
            'actor_id' => $actorId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'metadata' => $metadata ? json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
        ]);
    }
}
