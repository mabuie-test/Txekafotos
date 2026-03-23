<!doctype html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? 'Admin') . ' | ' . $appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="admin-body">
<div class="d-flex min-vh-100">
    <?php require base_path('app/Views/partials/admin-sidebar.php'); ?>
    <div class="flex-grow-1">
        <header class="border-bottom bg-white px-4 py-3 d-flex justify-content-between align-items-center">
            <h1 class="h4 mb-0">Painel Administrativo</h1>
            <form method="post" action="/admin/logout" class="mb-0">
                <?= \App\Core\Csrf::field() ?>
                <button class="btn btn-outline-danger btn-sm">Sair</button>
            </form>
        </header>
        <main class="p-4"><?= $content ?></main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
