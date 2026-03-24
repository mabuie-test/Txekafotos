<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Feedback;

class FeedbackService
{
    public function __construct(
        private readonly Feedback $feedbacks = new Feedback(),
        private readonly AuditService $auditService = new AuditService(),
    ) {
    }

    public function create(array $data): int
    {
        $id = $this->feedbacks->create($data);
        $this->auditService->log('client', null, 'feedback.created', 'feedback', $id, 'Feedback submetido pelo cliente.', ['order_id' => $data['order_id']]);
        return $id;
    }

    public function togglePublication(int $id, ?int $adminId = null): bool
    {
        $result = $this->feedbacks->toggle($id);
        $this->auditService->log('admin', $adminId, 'feedback.toggled', 'feedback', $id, 'Publicação do feedback alterada.');
        return $result;
    }
}
