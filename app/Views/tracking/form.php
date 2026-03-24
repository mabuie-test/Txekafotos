<?php $title = 'Acompanhar pedido'; ?>
<section class="section-shell">
    <div class="container">
        <div class="row justify-content-center align-items-center g-4">
            <div class="col-lg-5">
                <div class="section-heading mb-0">
                    <span class="eyebrow text-primary">Tracking</span>
                    <h1 class="mt-2 mb-3">Consulte o estado do seu pedido em segundos.</h1>
                    <p class="text-muted mb-0">Use o ID do pedido e o telefone usado no pagamento M-Pesa para ver o progresso, revisões e entrega.</p>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="surface-card">
                    <?php if (!empty($error)): ?><div class="alert alert-danger border-0 rounded-4"><i class="fa-solid fa-circle-exclamation me-2"></i><?= e($error) ?></div><?php endif; ?>
                    <form method="post" action="/acompanhar" class="row g-3">
                        <?= \App\Core\Csrf::field() ?>
                        <div class="col-12">
                            <label class="form-label fw-semibold">ID do pedido</label>
                            <div class="form-icon-field">
                                <i class="fa-solid fa-hashtag field-icon"></i>
                                <input type="number" name="order_id" class="form-control" placeholder="Ex.: 1024" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Telefone</label>
                            <div class="form-icon-field">
                                <i class="fa-solid fa-phone field-icon"></i>
                                <input type="text" name="client_phone" class="form-control" placeholder="25884XXXXXXX" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary btn-lg w-100"><i class="fa-solid fa-magnifying-glass me-2"></i>Consultar status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
