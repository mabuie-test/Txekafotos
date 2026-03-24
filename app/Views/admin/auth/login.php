<?php $title = 'Entrar no painel'; ?>
<div class="row justify-content-center align-items-center min-vh-100 g-4">
    <div class="col-lg-5">
        <div class="text-white mb-4 text-center text-lg-start">
            <span class="eyebrow text-warning">Admin</span>
            <h1 class="display-6 fw-bold mt-2 mb-3">Gestão moderna para pedidos, finanças e experiência do cliente.</h1>
            <p class="text-white-50 mb-0">Aceda ao painel administrativo para acompanhar a operação, publicar showcases e gerir revisões em tempo real.</p>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="login-card">
            <div class="d-flex align-items-center gap-3 mb-4">
                <span class="feature-icon"><i class="fa-solid fa-user-shield"></i></span>
                <div>
                    <h2 class="h3 mb-1">Entrar no painel</h2>
                    <p class="text-muted mb-0">Acesso restrito à equipa administrativa.</p>
                </div>
            </div>
            <form method="post" action="/admin/login" class="row g-3">
                <?= \App\Core\Csrf::field() ?>
                <div class="col-12">
                    <label class="form-label fw-semibold">Email</label>
                    <div class="form-icon-field">
                        <i class="fa-solid fa-envelope field-icon"></i>
                        <input type="email" name="email" class="form-control" required placeholder="admin@txekafotos.com">
                    </div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold">Senha</label>
                    <div class="form-icon-field">
                        <i class="fa-solid fa-lock field-icon"></i>
                        <input type="password" name="password" class="form-control" required placeholder="••••••••">
                    </div>
                </div>
                <div class="col-12 d-grid">
                    <button class="btn btn-primary btn-lg"><i class="fa-solid fa-right-to-bracket me-2"></i>Entrar</button>
                </div>
            </form>
        </div>
    </div>
</div>
