<?php $title = 'Acompanhar pedido'; ?>
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-lg-5">
                        <h1 class="h3 mb-3">Acompanhar pedido</h1>
                        <p class="text-muted">Use o ID do pedido e o telefone usado no pagamento M-Pesa.</p>
                        <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                        <form method="post" action="/acompanhar" class="row g-3">
                            <?= \App\Core\Csrf::field() ?>
                            <div class="col-12">
                                <label class="form-label">ID do pedido</label>
                                <input type="number" name="order_id" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Telefone</label>
                                <input type="text" name="client_phone" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <button class="btn btn-dark w-100">Consultar status</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
