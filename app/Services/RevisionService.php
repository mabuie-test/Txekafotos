<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Order;
use App\Models\Revision;
use RuntimeException;

class RevisionService
{
    public function __construct(
        private readonly Revision $revisions = new Revision(),
        private readonly Order $orders = new Order(),
        private readonly AuditService $auditService = new AuditService(),
        private readonly OrderService $orderService = new OrderService(),
    ) {
    }

    public function requestRevision(int $orderId, string $message): int
    {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Pedido não encontrado.');
        }
        $maxRevisions = (int) config('services.orders.max_revisions', 2);
        if ((int) $order['revisions_used'] >= $maxRevisions) {
            throw new RuntimeException('Limite máximo de revisões atingido.');
        }
        if ($order['status'] !== 'concluido') {
            throw new RuntimeException('Só é possível pedir revisão após a conclusão inicial.');
        }

        $revisionId = $this->revisions->create([
            'order_id' => $orderId,
            'client_message' => trim($message),
            'admin_response' => null,
            'status' => 'pending',
        ]);
        Database::instance()->execute('UPDATE orders SET status = :status, revisions_used = revisions_used + 1 WHERE id = :id', ['status' => 'revisao', 'id' => $orderId]);
        $this->auditService->log('client', null, 'revision.requested', 'revision', $revisionId, 'Cliente solicitou reedição.', ['order_id' => $orderId]);
        return $revisionId;
    }

    public function respond(int $revisionId, string $response, ?int $adminId = null): bool
    {
        $result = $this->revisions->respond($revisionId, $response);
        $revision = $this->revisions->find($revisionId);
        if ($revision) {
            Database::instance()->execute("UPDATE orders SET status = 'em_edicao' WHERE id = :id", ['id' => $revision['order_id']]);
        }
        $this->auditService->log('admin', $adminId, 'revision.answered', 'revision', $revisionId, 'Resposta enviada para revisão.');
        return $result;
    }
}
