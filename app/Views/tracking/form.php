<?php $title = 'Acompanhar pedido'; ?>
<section class="section-shell">
    <div class="container">
        <div class="row justify-content-center align-items-start g-4">
            <div class="col-lg-5">
                <div class="section-heading mb-4">
                    <span class="eyebrow text-primary">Tracking</span>
                    <h1 class="mt-2 mb-3">Consulte todos os seus pedidos usando apenas o número do celular.</h1>
                    <p class="text-muted mb-0">Informe o mesmo número usado no pedido/pagamento e veja a lista completa de trabalhos associados a esse contacto.</p>
                </div>
                <div class="surface-card">
                    <?php if (!empty($error)): ?><div class="alert alert-danger border-0 rounded-4"><i class="fa-solid fa-circle-exclamation me-2"></i><?= e($error) ?></div><?php endif; ?>
                    <form method="post" action="/acompanhar" class="row g-3">
                        <?= \App\Core\Csrf::field() ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Telefone</label>
                            <div class="form-icon-field">
                                <i class="fa-solid fa-phone field-icon"></i>
                                <input type="text" name="client_phone" class="form-control" placeholder="25884XXXXXXX" value="<?= e($clientPhone ?? '') ?>" required>
                            </div>
                            <small class="text-muted">Você não precisa mais informar o ID do pedido para consultar.</small>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary btn-lg w-100"><i class="fa-solid fa-magnifying-glass me-2"></i>Ver meus pedidos</button>
                        </div>
                    </form>
                </div>

                <div class="surface-card mt-4">
                    <span class="eyebrow text-primary">Contacte-nos</span>
                    <h2 class="h5 mt-2 mb-3">Precisa de ajuda com o pedido?</h2>
                    <div class="d-grid gap-3">
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
                </div>
            </div>

            <div class="col-lg-7">
                <?php if (!empty($orders)): ?>
                    <div class="surface-card">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
                            <div>
                                <span class="eyebrow text-primary">Pedidos encontrados</span>
                                <h2 class="h4 mt-2 mb-1">Encontrámos <?= e((string) count($orders)) ?> pedido(s) para <?= e($clientPhone ?? '') ?></h2>
                                <p class="text-muted mb-0">Escolha um pedido para abrir o detalhe completo ou continuar o pagamento se ele ainda estiver pendente.</p>
                            </div>
                        </div>
                        <div class="d-grid gap-3">
                            <?php foreach ($orders as $order): ?>
                                <article class="detail-item">
                                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                                        <div>
                                            <strong class="d-block">Pedido #<?= e((string) $order['id']) ?> · <?= e($order['tracking_code']) ?></strong>
                                            <span class="text-muted small">Criado em <?= e($order['created_at']) ?></span>
                                        </div>
                                        <span class="badge <?= e(status_badge_class((string) $order['status'])) ?> rounded-pill"><i class="<?= e(status_icon_class((string) $order['status'])) ?> me-1"></i><?= e($order['status']) ?></span>
                                    </div>
                                    <div class="small text-muted mb-3">
                                        Serviço: <strong><?= e($order['service_type'] ?: 'Personalizado') ?></strong> · Valor: <strong><?= e(number_format((float) $order['amount'], 2)) ?> MZN</strong>
                                    </div>
                                    <div class="action-group">
                                        <a href="/pedido/<?= e((string) $order['id']) ?>/status" class="btn btn-outline-primary"><i class="fa-solid fa-eye me-2"></i>Ver acompanhamento</a>
                                        <?php if (in_array($order['status'], ['pendente_pagamento', 'pagamento_em_analise', 'falhou_pagamento'], true)): ?>
                                            <a href="/pedido/<?= e((string) $order['id']) ?>/pagamento" class="btn btn-success"><i class="fa-solid fa-credit-card me-2"></i>Ir para pagamento</a>
                                        <?php endif; ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
