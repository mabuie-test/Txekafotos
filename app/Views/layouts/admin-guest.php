<!doctype html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(($title ?? 'Login Admin') . ' | ' . $appName) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
</head>
<body class="bg-dark-subtle d-flex align-items-center min-vh-100">
<div class="container">
    <?php require base_path('app/Views/partials/alerts.php'); ?>
    <?= $content ?>
</div>
</body>
</html>
