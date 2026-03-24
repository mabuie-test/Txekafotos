<?php $title = 'Status do pedido'; ?>
<?php $steps = ['pendente_pagamento','pagamento_em_analise','pago','em_edicao','revisao','concluido','aprovado']; ?>
<?php $currentStepIndex = array_search((string) $order['status'], $steps, true); ?>
<?php $currentStepIndex = $currentStepIndex === false ? 0 : $currentStepIndex; ?>
<?php $canPay = in_array((string) $order['status'], ['pendente_pagamento', 'pagamento_em_analise', 'falhou_pagamento'], true); ?>
<section class="section-shell">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <span class="badge-soft mb-2"><i class="fa-solid fa-box me-2"></i>Pedido #<?= e((string) $order['id']) ?></span>
                <h1 class="h2 mb-1">Acompanhamento do pedido</h1>
                <p class="text-muted mb-0">Tracking <?= e($order['tracking_code']) ?> · Veja o estado atual, o histórico e as próximas ações.</p>
            </div>
            <div class="action-group">
                <?php if ($canPay): ?>
                    <a href="/pedido/<?= e((string) $order['id']) ?>/pagamento" class="btn btn-success"><i class="fa-solid fa-credit-card me-2"></i>Pagar agora</a>
                <?php endif; ?>
                <a href="/acompanhar" class="btn btn-outline-primary"><i class="fa-solid fa-arrow-left me-2"></i>Nova consulta</a>
            </div>
        </div>
        <?php if (!empty($error)): ?><div class="alert alert-danger border-0 rounded-4"><i class="fa-solid fa-triangle-exclamation me-2"></i><?= e($error) ?></div><?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <article class="surface-card mb-4">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
                        <div>
                            <span class="eyebrow text-primary">Resumo</span>
                            <h2 class="h4 mt-2 mb-2">Estado atual do trabalho</h2>
                            <p class="text-muted mb-0"><?= e($order['description']) ?></p>
                        </div>
                        <span class="status-chip <?= e(status_badge_class((string) $order['status'])) ?>"><i class="<?= e(status_icon_class((string) $order['status'])) ?>"></i><?= e($order['status']) ?></span>
                    </div>
                    <?php if ($canPay): ?>
                        <div class="alert alert-warning border-0 rounded-4 mb-4"><i class="fa-solid fa-credit-card me-2"></i>Este pedido ainda não foi finalizado no pagamento. Use o botão <strong>Pagar agora</strong> para continuar.</div>
                    <?php endif; ?>
                    <div class="timeline">
                        <?php foreach ($steps as $step): ?>
                            <?php $stepIndex = array_search($step, $steps, true); ?>
                            <?php $isActive = $stepIndex !== false && $stepIndex <= $currentStepIndex; ?>
                            <div class="timeline-item <?= $isActive ? 'active' : '' ?>">
                                <span class="timeline-icon"><i class="<?= e(status_icon_class($step)) ?>"></i></span>
                                <div class="timeline-content">
                                    <strong class="d-block mb-1"><?= e(ucwords(str_replace('_', ' ', $step))) ?></strong>
                                    <span class="text-muted small"><?= $step === $order['status'] ? 'Etapa atual do pedido.' : 'Etapa do fluxo operacional.' ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <?php if (!empty($order['edited_image_path'])): ?>
                    <article class="surface-card mb-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                            <div>
                                <span class="eyebrow text-primary">Resultado</span>
                                <h2 class="h4 mt-2 mb-1">Imagem final entregue</h2>
                                <p class="text-muted mb-0">Visualize o trabalho final e escolha a próxima ação.</p>
                            </div>
                        </div>
                        <div class="order-image-frame mb-4">
                            <img src="<?= e(media_url($order['edited_image_path'])) ?>" alt="Imagem final" class="img-fluid rounded-4">
                            <span class="zoom-badge"><i class="fa-solid fa-expand"></i></span>
                        </div>
                        <div class="action-group">
                            <?php if ($order['status'] === 'concluido'): ?>
                                <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/aprovar">
                                    <?= \App\Core\Csrf::field() ?>
                                    <button class="btn btn-success"><i class="fa-solid fa-circle-check me-2"></i>Aprovar trabalho</button>
                                </form>
                                <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#revisionForm"><i class="fa-solid fa-rotate me-2"></i>Pedir reedição</button>
                            <?php endif; ?>
                            <?php if ($order['status'] === 'aprovado'): ?>
                                <a href="<?= e(media_url($order['edited_image_path'])) ?>" class="btn btn-success" download><i class="fa-solid fa-download me-2"></i>Baixar imagem final</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endif; ?>

                <div id="revisionForm" class="collapse <?= !empty($error) ? 'show' : '' ?> mb-4">
                    <article class="surface-card">
                        <span class="eyebrow text-primary">Revisão</span>
                        <h2 class="h4 mt-2 mb-3">Solicitar revisão</h2>
                        <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/revisao">
                            <?= \App\Core\Csrf::field() ?>
                            <div class="form-icon-field textarea-field mb-3">
                                <i class="fa-solid fa-rotate field-icon"></i>
                                <textarea class="form-control" name="message" rows="4" required placeholder="Explique o que ainda precisa ser ajustado."></textarea>
                            </div>
                            <button class="btn btn-primary"><i class="fa-solid fa-paper-plane me-2"></i>Enviar pedido de revisão</button>
                        </form>
                    </article>
                </div>

                <?php if ($order['status'] === 'aprovado' && empty($order['feedback'])): ?>
                    <article class="surface-card">
                        <span class="eyebrow text-primary">Feedback</span>
                        <h2 class="h4 mt-2 mb-3">Avaliar serviço</h2>
                        <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/feedback" class="row g-3">
                            <?= \App\Core\Csrf::field() ?>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nome</label>
                                <div class="form-icon-field">
                                    <i class="fa-solid fa-user field-icon"></i>
                                    <input type="text" name="client_name" class="form-control" value="<?= e($order['client_name']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nota</label>
                                <div class="form-icon-field">
                                    <i class="fa-solid fa-star field-icon"></i>
                                    <select name="rating" class="form-select" required>
                                        <option value="">Selecione</option>
                                        <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> estrela(s)</option><?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Mensagem</label>
                                <div class="form-icon-field textarea-field">
                                    <i class="fa-solid fa-quote-left field-icon"></i>
                                    <textarea name="message" class="form-control" rows="4" required placeholder="Conte como foi a sua experiência."></textarea>
                                </div>
                            </div>
                            <div class="col-12"><button class="btn btn-warning"><i class="fa-solid fa-star me-2"></i>Enviar feedback</button></div>
                        </form>
                    </article>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <aside class="surface-card mb-4">
                    <span class="eyebrow text-primary">Imagem original</span>
                    <div class="order-image-frame mt-3">
                        <img src="<?= e(media_url($order['primary_image_path'])) ?>" class="img-fluid rounded-4" alt="Foto principal">
                        <span class="zoom-badge"><i class="fa-solid fa-expand"></i></span>
                    </div>
                </aside>
                <aside class="surface-card mb-4">
                    <span class="eyebrow text-primary">Dados do pedido</span>
                    <div class="detail-list mt-3">
                        <div class="detail-item"><div class="small text-muted">Cliente</div><strong><?= e($order['client_name']) ?></strong></div>
                        <div class="detail-item"><div class="small text-muted">Telefone</div><strong><?= e($order['client_phone']) ?></strong></div>
                        <div class="detail-item"><div class="small text-muted">Serviço</div><strong><?= e($order['service_type'] ?: 'Personalizado') ?></strong></div>
                        <div class="detail-item"><div class="small text-muted">Revisões usadas</div><strong><?= e((string) $order['revisions_used']) ?>/<?= e((string) config('services.orders.max_revisions', 2)) ?></strong></div>
                    </div>
                </aside>
                <aside class="surface-card mb-4">
                    <span class="eyebrow text-primary">Contacte-nos</span>
                    <div class="d-grid gap-3 mt-3">
                        <?php if (!empty($contactPhone)): ?>
                            <a class="detail-item d-flex justify-content-between align-items-center text-dark" href="tel:<?= e(preg_replace('/\s+/', '', (string) $contactPhone) ?? '') ?>">
                                <span><i class="fa-solid fa-phone me-2 text-primary"></i>Telefone</span>
                                <strong><?= e($contactPhone) ?></strong>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($contactEmail)): ?>
                            <a class="detail-item d-flex justify-content-between align-items-center text-dark" href="mailto:<?= e($contactEmail) ?>">
                                <span><i class="fa-solid fa-envelope me-2 text-primary"></i>Email</span>
                                <strong><?= e($contactEmail) ?></strong>
                            </a>
                        <?php endif; ?>
                    </div>
                </aside>
                <aside class="surface-card">
                    <span class="eyebrow text-primary">Histórico de revisões</span>
                    <?php if (empty($order['revisions'])): ?>
                        <p class="text-muted mb-0 mt-3">Nenhuma revisão registada.</p>
                    <?php else: ?>
                        <div class="d-grid gap-3 mt-3">
                            <?php foreach ($order['revisions'] as $revision): ?>
                                <div class="detail-item">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <strong><?= e($revision['status']) ?></strong>
                                        <span class="small text-muted"><?= e($revision['created_at']) ?></span>
                                    </div>
                                    <p class="small mb-2"><?= e($revision['client_message']) ?></p>
                                    <?php if ($revision['admin_response']): ?><div class="small text-muted">Resposta admin: <?= e($revision['admin_response']) ?></div><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </div>
</section>
