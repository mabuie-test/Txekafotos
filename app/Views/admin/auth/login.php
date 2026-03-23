<div class="row justify-content-center">
    <div class="col-lg-4">
        <div class="card border-0 shadow-lg">
            <div class="card-body p-4 p-lg-5">
                <h1 class="h3 mb-3">Entrar no painel</h1>
                <p class="text-muted">Acesso restrito à equipa administrativa.</p>
                <?php if (!empty($error)): ?><div class="alert alert-danger"><?= e($error) ?></div><?php endif; ?>
                <form method="post" action="/admin/login" class="row g-3">
                    <?= \App\Core\Csrf::field() ?>
                    <div class="col-12"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="col-12"><label class="form-label">Senha</label><input type="password" name="password" class="form-control" required></div>
                    <div class="col-12"><button class="btn btn-dark w-100">Entrar</button></div>
                </form>
            </div>
        </div>
    </div>
</div>
