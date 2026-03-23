<?php $title = 'Status do pedido'; ?>
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="badge text-bg-warning mb-2">Pedido #<?= e((string) $order['id']) ?></span>
                <h1 class="h2 mb-0">Acompanhamento do pedido</h1>
            </div>
            <a href="/acompanhar" class="btn btn-outline-dark">Nova consulta</a>
        </div>
        <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <div>
                                <h2 class="h5 mb-1">Resumo do pedido</h2>
                                <p class="text-muted mb-0"><?= e($order['description']) ?></p>
                            </div>
                            <span class="badge text-bg-dark"><?= e($order['status']) ?></span>
                        </div>
                        <div class="timeline">
                            <?php foreach (['pendente_pagamento','pagamento_em_analise','pago','em_edicao','revisao','concluido','aprovado'] as $step): ?>
                                <div class="timeline-item <?= in_array($order['status'], [$step,'aprovado'], true) || $order['status'] === $step ? 'active' : '' ?>">
                                    <strong><?= e(ucwords(str_replace('_', ' ', $step))) ?></strong>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php if (!empty($order['edited_image_path'])): ?>
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 mb-3">Resultado entregue</h2>
                            <img src="<?= e(media_url($order['edited_image_path'])) ?>" alt="Imagem final" class="img-fluid rounded shadow-sm mb-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <?php if ($order['status'] === 'concluido'): ?>
                                    <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/aprovar">
                                        <?= \App\Core\Csrf::field() ?>
                                        <button class="btn btn-success">Aprovar trabalho</button>
                                    </form>
                                    <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#revisionForm">Pedir reedição</button>
                                <?php endif; ?>
                                <?php if ($order['status'] === 'aprovado'): ?>
                                    <a href="<?= e(media_url($order['edited_image_path'])) ?>" class="btn btn-success" download>Baixar imagem final</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <div id="revisionForm" class="collapse <?= !empty($error) ? 'show' : '' ?>">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5">Solicitar revisão</h2>
                            <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/revisao">
                                <?= \App\Core\Csrf::field() ?>
                                <textarea class="form-control mb-3" name="message" rows="4" required placeholder="Explique o que ainda precisa ser ajustado."></textarea>
                                <button class="btn btn-dark">Enviar pedido de revisão</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php if ($order['status'] === 'aprovado' && empty($order['feedback'])): ?>
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <h2 class="h5">Avaliar serviço</h2>
                            <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/feedback" class="row g-3">
                                <?= \App\Core\Csrf::field() ?>
                                <div class="col-md-6">
                                    <input type="text" name="client_name" class="form-control" value="<?= e($order['client_name']) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <select name="rating" class="form-select" required>
                                        <option value="">Nota</option>
                                        <?php for ($i = 5; $i >= 1; $i--): ?><option value="<?= $i ?>"><?= $i ?> estrela(s)</option><?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <textarea name="message" class="form-control" rows="4" required placeholder="Conte como foi a sua experiência."></textarea>
                                </div>
                                <div class="col-12"><button class="btn btn-warning">Enviar feedback</button></div>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 text-uppercase text-muted">Imagem principal</h2>
                        <img src="<?= e(media_url($order['primary_image_path'])) ?>" class="img-fluid rounded shadow-sm" alt="Foto principal">
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h6 text-uppercase text-muted">Dados</h2>
                        <ul class="list-unstyled mb-0">
                            <li><strong>Cliente:</strong> <?= e($order['client_name']) ?></li>
                            <li><strong>Telefone:</strong> <?= e($order['client_phone']) ?></li>
                            <li><strong>Serviço:</strong> <?= e($order['service_type'] ?: 'Personalizado') ?></li>
                            <li><strong>Revisões usadas:</strong> <?= e((string) $order['revisions_used']) ?>/<?= e((string) config('services.orders.max_revisions', 2)) ?></li>
                        </ul>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h6 text-uppercase text-muted">Histórico de revisões</h2>
                        <?php if (empty($order['revisions'])): ?>
                            <p class="text-muted mb-0">Nenhuma revisão registada.</p>
                        <?php else: ?>
                            <div class="d-grid gap-3">
                                <?php foreach ($order['revisions'] as $revision): ?>
                                    <div class="border rounded p-3">
                                        <strong class="d-block mb-1"><?= e($revision['status']) ?></strong>
                                        <p class="small mb-1"><?= e($revision['client_message']) ?></p>
                                        <?php if ($revision['admin_response']): ?><div class="small text-muted">Resposta admin: <?= e($revision['admin_response']) ?></div><?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
