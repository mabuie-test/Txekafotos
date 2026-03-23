<?php
$currentPath = $_SERVER['REQUEST_URI'] ?? '/admin';
$navItems = [
    ['/admin', 'Dashboard', 'fa-chart-line'],
    ['/admin/pedidos', 'Pedidos', 'fa-list'],
    ['/admin/financeiro', 'Financeiro', 'fa-money-bill-wave'],
    ['/admin/revisoes', 'Revisões', 'fa-rotate'],
    ['/admin/feedbacks', 'Feedback', 'fa-star'],
    ['/admin/showcases', 'Antes/Depois', 'fa-images'],
    ['/admin/marketing', 'Marketing', 'fa-bullhorn'],
    ['/admin/relatorios', 'Relatórios', 'fa-file-lines'],
];
?>
<div class="offcanvas-lg offcanvas-start admin-sidebar offcanvas" tabindex="-1" id="adminSidebarOffcanvas" aria-labelledby="adminSidebarLabel">
    <div class="offcanvas-header border-bottom border-white border-opacity-10 d-lg-none">
        <h5 class="offcanvas-title" id="adminSidebarLabel">Txekafotos Admin</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Fechar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column p-3 p-lg-4">
        <div class="admin-brand mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="brand-mark"><i class="fa-solid fa-camera-retro"></i></span>
                <div>
                    <h2 class="h5 mb-0 text-white">Txekafotos</h2>
                    <p class="text-white-50 small mb-0">Painel operacional</p>
                </div>
            </div>
            <div class="admin-sidebar-card">
                <div class="small text-white-50 mb-1">Resumo rápido</div>
                <div class="fw-semibold text-white">Pedidos, finanças e marketing em um único fluxo.</div>
            </div>
        </div>

        <nav class="nav flex-column gap-2 admin-nav">
            <?php foreach ($navItems as [$href, $label, $icon]): ?>
                <?php $isActive = $href === '/admin' ? $currentPath === '/admin' : str_starts_with($currentPath, $href); ?>
                <a class="nav-link <?= $isActive ? 'active' : '' ?>" href="<?= e($href) ?>">
                    <i class="fa-solid <?= e($icon) ?>"></i>
                    <span><?= e($label) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <div class="mt-auto pt-4">
            <form method="post" action="/admin/logout">
                <?= \App\Core\Csrf::field() ?>
                <button class="btn btn-outline-light w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
            </form>
        </div>
    </div>
</div>
