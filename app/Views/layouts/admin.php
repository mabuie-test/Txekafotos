<!doctype html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? 'Admin') . ' | ' . $appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="app-shell admin-shell">
<div class="admin-layout d-flex min-vh-100">
    <?php require base_path('app/Views/partials/admin-sidebar.php'); ?>
    <div class="admin-content flex-grow-1">
        <header class="admin-topbar">
            <div class="container-fluid px-3 px-lg-4">
                <div class="d-flex align-items-center justify-content-between gap-3 py-3">
                    <div class="d-flex align-items-center gap-3">
                        <button class="btn btn-outline-primary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#adminSidebarOffcanvas" aria-controls="adminSidebarOffcanvas">
                            <i class="fa-solid fa-bars"></i>
                        </button>
                        <div>
                            <span class="eyebrow text-primary">Painel administrativo</span>
                            <h1 class="h4 mb-0">Operação interna Txekafotos</h1>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="admin-user-chip text-end d-none d-sm-flex flex-column">
                            <span class="fw-semibold"><i class="fa-solid fa-user-circle me-1 text-primary"></i><?= e(\App\Core\Auth::user()['name'] ?? 'Admin') ?></span>
                            <span class="text-muted small"><?= e(\App\Core\Auth::user()['email'] ?? '') ?></span>
                        </div>
                        <form method="post" action="/admin/logout" class="mb-0">
                            <?= \App\Core\Csrf::field() ?>
                            <button class="btn btn-outline-danger btn-sm px-3"><i class="fa-solid fa-right-from-bracket me-2"></i>Sair</button>
                        </form>
                    </div>
                </div>
            </div>
        </header>
        <main class="admin-page p-3 p-lg-4">
            <div class="container-fluid px-0">
                <?php require base_path('app/Views/partials/alerts.php'); ?>
                <?= $content ?>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= e(asset('js/app.js')) ?>"></script>
</body>
</html>
