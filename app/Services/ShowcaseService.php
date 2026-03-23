<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Showcase;

class ShowcaseService
{
    public function __construct(
        private readonly Showcase $showcases = new Showcase(),
        private readonly AuditService $auditService = new AuditService(),
    ) {
    }

    public function create(array $data, ?int $adminId = null): int
    {
        $id = $this->showcases->create($data);
        $this->auditService->log('admin', $adminId, 'showcase.created', 'showcase', $id, 'Showcase publicado.', ['order_id' => $data['order_id']]);
        return $id;
    }

    public function toggle(int $id, ?int $adminId = null): bool
    {
        $result = $this->showcases->toggle($id);
        $this->auditService->log('admin', $adminId, 'showcase.toggled', 'showcase', $id, 'Status de publicação do showcase alterado.');
        return $result;
    }
}
