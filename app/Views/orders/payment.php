<?php $title = 'Pagamento do pedido'; ?>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-warning mb-3">Pedido #<?= e((string) $order['id']) ?> · <?= e($order['tracking_code']) ?></span>
                        <h1 class="h3">Confirmar pagamento via M-Pesa</h1>
                        <p class="text-muted">Use o mesmo número M-Pesa informado na criação do pedido e depois sincronize o estado da cobrança.</p>
                        <?php if (!empty($flash)): ?><div class="alert alert-success mt-3"><?= e($flash) ?></div><?php endif; ?>
                        <?php if (!empty($error)): ?><div class="alert alert-danger mt-3"><?= e($error) ?></div><?php endif; ?>
                        <dl class="row mt-4 mb-4">
                            <dt class="col-sm-4">Cliente</dt><dd class="col-sm-8"><?= e($order['client_name']) ?></dd>
                            <dt class="col-sm-4">Telefone</dt><dd class="col-sm-8"><?= e($order['client_phone']) ?></dd>
                            <dt class="col-sm-4">Serviço</dt><dd class="col-sm-8"><?= e($order['service_type'] ?: 'Personalizado') ?></dd>
                            <dt class="col-sm-4">Valor</dt><dd class="col-sm-8 fw-bold"><?= e(number_format((float) $order['amount'], 2)) ?> MZN</dd>
                            <dt class="col-sm-4">Status do pedido</dt><dd class="col-sm-8"><span class="badge text-bg-secondary"><?= e($order['status']) ?></span></dd>
                        </dl>
                        <div class="d-flex flex-wrap gap-3">
                            <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/iniciar-pagamento">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="btn btn-success btn-lg">Iniciar cobrança M-Pesa</button>
                            </form>
                            <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/verificar-pagamento">
                                <?= \App\Core\Csrf::field() ?>
                                <button class="btn btn-outline-dark btn-lg">Verificar pagamento</button>
                            </form>
                            <a href="/pedido/<?= e((string) $order['id']) ?>/status" class="btn btn-outline-secondary btn-lg">Acompanhar pedido</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Estado da transação</h2>
                        <?php if ($transaction): ?>
                            <ul class="list-unstyled mb-0 small">
                                <li class="mb-2"><strong>Referência Débito:</strong><br><?= e($transaction['debito_reference']) ?></li>
                                <li class="mb-2"><strong>Status interno:</strong> <?= e($transaction['status']) ?></li>
                                <li class="mb-2"><strong>Status gateway:</strong> <?= e($transaction['gateway_status'] ?? 'n/d') ?></li>
                                <li class="mb-2"><strong>MSISDN:</strong> <?= e($transaction['msisdn'] ?? $order['client_phone']) ?></li>
                                <li class="mb-0"><strong>Última verificação:</strong> <?= e($transaction['last_checked_at'] ?? 'ainda não verificado') ?></li>
                            </ul>
                            <?php if (!empty($transaction['failure_reason'])): ?><div class="alert alert-warning mt-3 mb-0"><?= e($transaction['failure_reason']) ?></div><?php endif; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">Nenhuma cobrança iniciada ainda para este pedido.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="h5 mb-3">Checklist do cliente</h2>
                        <ol class="small text-muted mb-0 ps-3">
                            <li>Clique em <strong>Iniciar cobrança M-Pesa</strong>.</li>
                            <li>Confirme a solicitação no telemóvel.</li>
                            <li>Depois clique em <strong>Verificar pagamento</strong>.</li>
                            <li>Quando o pedido ficar <strong>pago</strong>, ele entra na fila de edição.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
