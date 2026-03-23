<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Models\Order;
use App\Models\OrderImage;
use RuntimeException;

class OrderService
{
    private readonly Order $orders;
    private readonly OrderImage $images;
    private readonly UploadService $uploadService;
    private readonly AuditService $auditService;

    public const SERVICE_TYPES = [
        'restauracao',
        'montagem',
        'inserir_pessoa',
        'juntar_familiares',
        'melhorar_qualidade',
        'remover_fundo',
        'colorizacao',
        'outro',
    ];

    private const STATUS_TRANSITIONS = [
        'pendente_pagamento' => ['pagamento_em_analise', 'pago', 'falhou_pagamento', 'cancelado'],
        'pagamento_em_analise' => ['pago', 'falhou_pagamento', 'cancelado'],
        'pago' => ['em_edicao', 'cancelado'],
        'em_edicao' => ['concluido', 'revisao', 'cancelado'],
        'revisao' => ['em_edicao', 'concluido', 'cancelado'],
        'concluido' => ['aprovado', 'revisao'],
        'aprovado' => [],
        'cancelado' => [],
        'falhou_pagamento' => ['pendente_pagamento', 'pagamento_em_analise'],
    ];

    public function __construct(
        ?Order $orders = null,
        ?OrderImage $images = null,
        ?UploadService $uploadService = null,
        ?AuditService $auditService = null,
    ) {
        $this->orders = $orders ?? new Order();
        $this->images = $images ?? new OrderImage();
        $this->uploadService = $uploadService ?? new UploadService(config('services.upload', []));
        $this->auditService = $auditService ?? new AuditService();
    }

    public function createOrder(array $payload, array $primaryFile, ?array $extraFiles = null): int
    {
        $cleanPayload = $this->validateCreatePayload($payload);
        $db = Database::instance();
        $storedFiles = [];

        $db->beginTransaction();

        try {
            $primaryImage = $this->uploadService->storeSingle($primaryFile, 'original');
            $storedFiles[] = $primaryImage;

            $trackingCode = $this->generateUniqueTrackingCode();
            $orderId = $this->orders->create([
                'tracking_code' => $trackingCode,
                'tracking_token' => hash('sha256', $trackingCode . '|' . bin2hex(random_bytes(12))),
                'client_name' => $cleanPayload['client_name'],
                'client_phone' => $cleanPayload['client_phone'],
                'service_type' => $cleanPayload['service_type'],
                'description' => $cleanPayload['description'],
                'primary_image_path' => $primaryImage['file_path'],
                'amount' => (float) config('app.base_price', 45),
                'status' => 'pendente_pagamento',
                'terms_accepted' => 1,
                'revisions_used' => 0,
                'internal_notes' => null,
            ]);

            $this->images->create([
                'order_id' => $orderId,
                'image_type' => 'primary',
                'file_path' => $primaryImage['file_path'],
                'original_name' => $primaryImage['original_name'],
                'mime_type' => $primaryImage['mime_type'],
                'file_size' => $primaryImage['file_size'],
                'sort_order' => 0,
            ]);

            $extras = $extraFiles ? $this->uploadService->storeMultiple($extraFiles, 'extra', (int) config('services.upload.max_extra_images', 5)) : [];
            $storedFiles = [...$storedFiles, ...$extras];

            foreach ($extras as $extra) {
                $this->images->create([
                    'order_id' => $orderId,
                    'image_type' => 'extra',
                    'file_path' => $extra['file_path'],
                    'original_name' => $extra['original_name'],
                    'mime_type' => $extra['mime_type'],
                    'file_size' => $extra['file_size'],
                    'sort_order' => $extra['sort_order'],
                ]);
            }

            $this->auditService->log('client', null, 'order.created', 'order', $orderId, 'Novo pedido criado pelo cliente.', [
                'tracking_code' => $trackingCode,
                'extra_images_count' => count($extras),
                'service_type' => $cleanPayload['service_type'],
            ]);

            $db->commit();
            return $orderId;
        } catch (\Throwable $exception) {
            if ($db->pdo()->inTransaction()) {
                $db->rollBack();
            }
            $this->uploadService->deleteStoredFiles($storedFiles);
            throw $exception;
        }
    }

    public function transitionStatus(int $orderId, string $newStatus, ?int $adminId = null): bool
    {
        $order = $this->orders->find($orderId);
        if (!$order) {
            throw new RuntimeException('Pedido não encontrado.');
        }

        $currentStatus = $order['status'];
        if ($currentStatus === $newStatus) {
            return true;
        }

        if (!in_array($newStatus, self::STATUS_TRANSITIONS[$currentStatus] ?? [], true)) {
            throw new RuntimeException("Transição de status inválida: {$currentStatus} -> {$newStatus}");
        }

        $this->orders->updateStatus($orderId, $newStatus);
        $this->auditService->log($adminId ? 'admin' : 'system', $adminId, 'order.status_changed', 'order', $orderId, "Status alterado para {$newStatus}.", ['from' => $currentStatus, 'to' => $newStatus]);
        return true;
    }

    public function uploadFinalImage(int $orderId, array $file, ?int $adminId = null): string
    {
        $saved = $this->uploadService->storeSingle($file, 'edited');
        $this->orders->updateEditedImage($orderId, $saved['file_path']);
        $this->images->create([
            'order_id' => $orderId,
            'image_type' => 'edited',
            'file_path' => $saved['file_path'],
            'original_name' => $saved['original_name'],
            'mime_type' => $saved['mime_type'],
            'file_size' => $saved['file_size'],
            'sort_order' => 0,
        ]);
        $this->auditService->log($adminId ? 'admin' : 'system', $adminId, 'order.final_image_uploaded', 'order', $orderId, 'Imagem final enviada para o pedido.');
        return $saved['file_path'];
    }

    public function approve(int $orderId): void
    {
        $this->transitionStatus($orderId, 'aprovado');
        Database::instance()->execute('UPDATE orders SET approved_at = NOW() WHERE id = :id', ['id' => $orderId]);
    }

    public function validateCreatePayload(array $payload): array
    {
        $name = trim((string) ($payload['client_name'] ?? ''));
        $phone = preg_replace('/\D+/', '', (string) ($payload['client_phone'] ?? '')) ?? '';
        $serviceType = trim((string) ($payload['service_type'] ?? ''));
        $description = trim((string) ($payload['description'] ?? ''));
        $termsAccepted = in_array($payload['terms'] ?? null, ['1', 1, true, 'on'], true);

        $errors = [];
        if ($name === '' || mb_strlen($name) < 3) {
            $errors['client_name'][] = 'Informe um nome completo válido com pelo menos 3 caracteres.';
        }
        if (mb_strlen($name) > 150) {
            $errors['client_name'][] = 'O nome completo é demasiado longo.';
        }
        if ($phone === '') {
            $errors['client_phone'][] = 'O telefone M-Pesa é obrigatório.';
        }
        if ($description === '' || mb_strlen($description) < 20) {
            $errors['description'][] = 'Descreva o pedido com pelo menos 20 caracteres.';
        }
        if ($serviceType !== '' && !in_array($serviceType, self::SERVICE_TYPES, true)) {
            $errors['service_type'][] = 'Tipo de serviço inválido.';
        }
        if (!$termsAccepted) {
            $errors['terms'][] = 'Você deve aceitar os termos do serviço.';
        }

        if ($errors !== []) {
            throw new RuntimeException($this->flattenErrors($errors));
        }

        return [
            'client_name' => $name,
            'client_phone' => $phone,
            'service_type' => $serviceType !== '' ? $serviceType : null,
            'description' => $description,
        ];
    }

    private function generateUniqueTrackingCode(): string
    {
        do {
            $trackingCode = 'TXK-' . strtoupper(bin2hex(random_bytes(4)));
        } while ($this->orders->existsByTrackingCode($trackingCode));

        return $trackingCode;
    }

    private function flattenErrors(array $errors): string
    {
        $messages = [];
        foreach ($errors as $fieldErrors) {
            foreach ($fieldErrors as $message) {
                $messages[] = $message;
            }
        }

        return implode(' ', $messages);
    }
}
