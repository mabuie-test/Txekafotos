<?php $title = 'Pagamento do pedido'; ?>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <span class="badge text-bg-warning mb-3">Pedido #<?= e((string) $order['id']) ?></span>
                        <h1 class="h3">Confirme o pagamento via M-Pesa</h1>
                        <?php if (!empty($flash)): ?><div class="alert alert-info mt-3"><?= e($flash) ?></div><?php endif; ?>
                        <dl class="row mt-4 mb-4">
                            <dt class="col-sm-4">Cliente</dt><dd class="col-sm-8"><?= e($order['client_name']) ?></dd>
                            <dt class="col-sm-4">Telefone</dt><dd class="col-sm-8"><?= e($order['client_phone']) ?></dd>
                            <dt class="col-sm-4">Valor</dt><dd class="col-sm-8 fw-bold"><?= e(number_format((float) $order['amount'], 2)) ?> MZN</dd>
                            <dt class="col-sm-4">Status</dt><dd class="col-sm-8"><span class="badge text-bg-secondary"><?= e($order['status']) ?></span></dd>
                        </dl>
                        <form method="post" action="/pedido/<?= e((string) $order['id']) ?>/iniciar-pagamento">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-success btn-lg">Iniciar pagamento M-Pesa</button>
                            <a href="/pedido/<?= e((string) $order['id']) ?>/status" class="btn btn-outline-dark btn-lg ms-2">Acompanhar pedido</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
