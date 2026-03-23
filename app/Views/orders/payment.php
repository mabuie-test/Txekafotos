<?php $title = 'Pagamento do pedido'; ?>
<section class="section-shell">
    <div class="container">
        <div class="row justify-content-center g-4">
            <div class="col-lg-7">
                <article class="surface-card h-100">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <span class="badge-soft mb-2"><i class="fa-solid fa-receipt"></i> Pedido #<?= e((string) $order['id']) ?> · <?= e($order['tracking_code']) ?></span>
                            <h1 class="h3 mb-2">Confirmar pagamento via M-Pesa</h1>
                            <p class="text-muted mb-0">Use o mesmo número informado no pedido e sincronize o estado da cobrança quando confirmar no telemóvel.</p>
                        </div>
                        <span class="status-chip <?= e(status_badge_class((string) $order['status'])) ?>"><i class="<?= e(status_icon_class((string) $order['status'])) ?>"></i><?= e($order['status']) ?></span>
                    </div>
                    <div class="detail-list mb-4">
                        <div class="detail-item"><div class="small text-muted">Cliente</div><strong><?= e($order['client_name']) ?></strong></div>
                        <div class="detail-item"><div class="small text-muted">Telefone</div><strong><?= e($order['client_phone']) ?></strong></div>
                        <div class="detail-item"><div class="small text-muted">Serviço</div><strong><?= e($order['service_type'] ?: 'Personalizado') ?></strong></div>
                        <div class="detail-item"><div class="small text-muted">Valor</div><strong><?= e(number_format((float) $order['amount'], 2)) ?> MZN</strong></div>
                    </div>
                    <div class="action-group">
                        <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/iniciar-pagamento">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-success btn-lg"><i class="fa-solid fa-credit-card me-2"></i>Iniciar cobrança M-Pesa</button>
                        </form>
                        <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/verificar-pagamento">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-outline-primary btn-lg"><i class="fa-solid fa-rotate me-2"></i>Verificar pagamento</button>
                        </form>
                        <a href="/pedido/<?= e((string) $order['id']) ?>/status" class="btn btn-outline-dark btn-lg"><i class="fa-solid fa-clock-rotate-left me-2"></i>Acompanhar pedido</a>
                    </div>
                </article>
            </div>
            <div class="col-lg-5">
                <div class="surface-card mb-4">
                    <h2 class="h5 mb-3"><i class="fa-solid fa-signal me-2 text-primary"></i>Estado da transação</h2>
                    <?php if ($transaction): ?>
                        <div class="detail-list">
                            <div class="detail-item"><div class="small text-muted">Referência Débito</div><strong><?= e($transaction['debito_reference']) ?></strong></div>
                            <div class="detail-item"><div class="small text-muted">Status interno</div><span class="status-chip <?= e(status_badge_class((string) $transaction['status'])) ?>"><i class="<?= e(status_icon_class((string) $transaction['status'])) ?>"></i><?= e($transaction['status']) ?></span></div>
                            <div class="detail-item"><div class="small text-muted">Status gateway</div><strong><?= e($transaction['gateway_status'] ?? 'n/d') ?></strong></div>
                            <div class="detail-item"><div class="small text-muted">Última verificação</div><strong><?= e($transaction['last_checked_at'] ?? 'ainda não verificado') ?></strong></div>
                        </div>
                        <?php if (!empty($transaction['failure_reason'])): ?><div class="alert alert-warning border-0 rounded-4 mt-3 mb-0"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= e($transaction['failure_reason']) ?></div><?php endif; ?>
                    <?php else: ?>
                        <p class="text-muted mb-0">Nenhuma cobrança iniciada ainda para este pedido.</p>
                    <?php endif; ?>
                </div>
                <div class="surface-card">
                    <h2 class="h5 mb-3"><i class="fa-solid fa-list-check me-2 text-primary"></i>Checklist do cliente</h2>
                    <div class="d-grid gap-3 text-muted">
                        <div><strong class="d-block text-dark">1. Inicie a cobrança</strong><span class="small">Clique em “Iniciar cobrança M-Pesa”.</span></div>
                        <div><strong class="d-block text-dark">2. Confirme no telemóvel</strong><span class="small">Aceite a solicitação de pagamento no dispositivo.</span></div>
                        <div><strong class="d-block text-dark">3. Verifique o estado</strong><span class="small">Clique em “Verificar pagamento” para atualizar o sistema.</span></div>
                        <div><strong class="d-block text-dark">4. Siga o progresso</strong><span class="small">Quando estiver pago, o pedido entra automaticamente na fila de edição.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
