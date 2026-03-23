<?php $success = flash_message('success'); ?>
<?php $error = flash_message('error'); ?>
<?php $message = flash_message('message'); ?>
<?php if ($success): ?><div class="alert alert-success shadow-sm"><?= e((string) $success) ?></div><?php endif; ?>
<?php if ($message): ?><div class="alert alert-info shadow-sm"><?= e((string) $message) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger shadow-sm"><?= e((string) $error) ?></div><?php endif; ?>
