<?php if (!$order): ?>
    <div class="alert alert-danger border-0 rounded-4"><i class="fa-solid fa-triangle-exclamation me-2"></i>Pedido não encontrado.</div>
<?php else: ?>
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <span class="eyebrow text-primary">Pedido</span>
        <h2 class="h3 mb-1">Pedido #<?= e((string) $order['id']) ?></h2>
        <p class="text-muted mb-0">Tracking <?= e($order['tracking_code']) ?> · Criado em <?= e($order['created_at']) ?></p>
    </div>
    <span class="status-chip <?= e(status_badge_class((string) $order['status'])) ?>"><i class="<?= e(status_icon_class((string) $order['status'])) ?>"></i><?= e($order['status']) ?></span>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="admin-card mb-4">
            <h3 class="h5 mb-3"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Resumo do pedido</h3>
            <p class="mb-4"><?= e($order['description']) ?></p>
            <div class="row g-3">
                <div class="col-md-6"><div class="detail-item"><div class="small text-muted">Cliente</div><strong><?= e($order['client_name']) ?></strong></div></div>
                <div class="col-md-6"><div class="detail-item"><div class="small text-muted">Telefone</div><strong><?= e($order['client_phone']) ?></strong></div></div>
                <div class="col-md-6"><div class="detail-item"><div class="small text-muted">Serviço</div><strong><?= e($order['service_type'] ?: 'Personalizado') ?></strong></div></div>
                <div class="col-md-6"><div class="detail-item"><div class="small text-muted">Valor</div><strong><?= e(number_format((float) $order['amount'], 2)) ?> MZN</strong></div></div>
                <div class="col-md-6"><div class="detail-item"><div class="small text-muted">Revisões usadas</div><strong><?= e((string) $order['revisions_used']) ?></strong></div></div>
                <div class="col-md-6"><div class="detail-item"><div class="small text-muted">Pagamento confirmado</div><strong><?= e($order['payment_confirmed_at'] ?? 'Ainda não') ?></strong></div></div>
            </div>
        </div>

        <div class="admin-card mb-4">
            <h3 class="h5 mb-3"><i class="fa-solid fa-bolt me-2 text-primary"></i>Ações rápidas</h3>
            <form method="post" action="/admin/pedidos/<?= e((string) $order['id']) ?>/status" class="row g-3 mb-4">
                <?= \App\Core\Csrf::field() ?>
                <div class="col-md-8"><select name="status" class="form-select"><?php foreach (\App\Models\Order::STATUSES as $status): ?><option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-4 d-grid"><button class="btn btn-primary"><i class="fa-solid fa-pen me-2"></i>Atualizar status</button></div>
            </form>
            <form method="post" action="/admin/pedidos/<?= e((string) $order['id']) ?>/upload-final" enctype="multipart/form-data" class="row g-3">
                <?= \App\Core\Csrf::field() ?>
                <div class="col-md-8"><input type="file" name="edited_image" class="form-control" required></div>
                <div class="col-md-4 d-grid"><button class="btn btn-success"><i class="fa-solid fa-upload me-2"></i>Enviar foto final</button></div>
            </form>
        </div>

        <div class="admin-card mb-4">
            <h3 class="h5 mb-3"><i class="fa-solid fa-images me-2 text-primary"></i>Galeria do pedido</h3>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="detail-item order-image-frame">
                        <div class="small text-muted mb-2">Foto principal</div>
                        <img src="<?= e(media_url($order['primary_image_path'])) ?>" class="img-fluid rounded-4" alt="Principal">
                        <span class="zoom-badge"><i class="fa-solid fa-expand"></i></span>
                    </div>
                </div>
                <?php foreach ($order['extra_images'] as $image): ?>
                    <div class="col-md-6">
                        <div class="detail-item order-image-frame">
                            <div class="small text-muted mb-2">Imagem adicional #<?= e((string) $image['sort_order']) ?></div>
                            <img src="<?= e(media_url($image['file_path'])) ?>" class="img-fluid rounded-4" alt="Extra">
                            <span class="zoom-badge"><i class="fa-solid fa-expand"></i></span>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if ($order['edited_image_path']): ?>
                    <div class="col-12">
                        <div class="detail-item order-image-frame bg-light">
                            <div class="small text-muted mb-2">Resultado final entregue</div>
                            <img src="<?= e(media_url($order['edited_image_path'])) ?>" class="img-fluid rounded-4" alt="Final">
                            <span class="zoom-badge"><i class="fa-solid fa-expand"></i></span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="admin-card">
            <h3 class="h5 mb-3"><i class="fa-solid fa-rotate me-2 text-primary"></i>Histórico de revisões</h3>
            <?php if (empty($order['revisions'])): ?>
                <p class="text-muted mb-0">Nenhuma revisão registada.</p>
            <?php else: ?>
                <div class="d-grid gap-3">
                    <?php foreach ($order['revisions'] as $revision): ?>
                        <div class="detail-item">
                            <div class="d-flex justify-content-between"><strong><?= e($revision['status']) ?></strong><span class="small text-muted"><?= e($revision['created_at']) ?></span></div>
                            <p class="mb-1 mt-2"><?= e($revision['client_message']) ?></p>
                            <?php if ($revision['admin_response']): ?><div class="small text-muted">Resposta do admin: <?= e($revision['admin_response']) ?></div><?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="admin-card mb-4">
            <h3 class="h6 text-uppercase text-muted mb-3">Transação principal</h3>
            <?php if (!empty($order['transaction'])): ?>
                <div class="detail-list">
                    <div class="detail-item"><div class="small text-muted">Referência</div><strong><?= e($order['transaction']['debito_reference']) ?></strong></div>
                    <div class="detail-item"><div class="small text-muted">Status</div><span class="badge <?= e(status_badge_class((string) $order['transaction']['status'])) ?> rounded-pill"><i class="<?= e(status_icon_class((string) $order['transaction']['status'])) ?> me-1"></i><?= e($order['transaction']['status']) ?></span></div>
                    <div class="detail-item"><div class="small text-muted">Gateway</div><strong><?= e($order['transaction']['gateway_status'] ?? 'n/d') ?></strong></div>
                    <div class="detail-item"><div class="small text-muted">Valor</div><strong><?= e(number_format((float) $order['transaction']['amount'], 2)) ?> MZN</strong></div>
                </div>
            <?php else: ?><p class="text-muted mb-0">Sem transação associada.</p><?php endif; ?>
        </div>
        <div class="admin-card mb-4">
            <h3 class="h6 text-uppercase text-muted mb-3">Histórico de transações</h3>
            <?php if (empty($order['transactions'])): ?>
                <p class="text-muted mb-0">Nenhuma transação registada.</p>
            <?php else: ?>
                <div class="d-grid gap-2">
                    <?php foreach ($order['transactions'] as $tx): ?>
                        <div class="detail-item">
                            <div class="fw-semibold small"><?= e($tx['debito_reference']) ?></div>
                            <div class="small text-muted"><?= e($tx['status']) ?> · <?= e(number_format((float) $tx['amount'], 2)) ?> MZN</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="admin-card">
            <h3 class="h6 text-uppercase text-muted mb-3">Auditoria</h3>
            <?php if (empty($order['activities'])): ?>
                <p class="text-muted mb-0">Sem logs administrativos.</p>
            <?php else: ?>
                <div class="d-grid gap-3">
                    <?php foreach ($order['activities'] as $activity): ?>
                        <div class="detail-item">
                            <div class="fw-semibold small"><?= e($activity['action']) ?></div>
                            <div class="small text-muted"><?= e($activity['description']) ?></div>
                            <div class="small text-muted mt-1"><?= e($activity['created_at']) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php endif; ?>
