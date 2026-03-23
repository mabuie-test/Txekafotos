<?php
$success = flash_message('success');
$error = flash_message('error');
$message = flash_message('message');
$alerts = [
    ['value' => $success, 'class' => 'success', 'icon' => 'fa-circle-check'],
    ['value' => $message, 'class' => 'info', 'icon' => 'fa-circle-info'],
    ['value' => $error, 'class' => 'danger', 'icon' => 'fa-triangle-exclamation'],
];
?>
<?php foreach ($alerts as $alert): ?>
    <?php if (!$alert['value']) { continue; } ?>
    <div class="alert alert-<?= e($alert['class']) ?> border-0 shadow-sm d-flex align-items-start gap-3 alert-modern alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="fa-solid <?= e($alert['icon']) ?>"></i></span>
        <div class="flex-grow-1"><?= e((string) $alert['value']) ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
<?php endforeach; ?>
