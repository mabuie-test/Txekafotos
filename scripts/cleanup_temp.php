<?php

declare(strict_types=1);

$directory = dirname(__DIR__) . '/storage/temp';
$maxAge = 60 * 60 * 24;
$removed = 0;

foreach (glob($directory . '/*') ?: [] as $file) {
    if (is_file($file) && (time() - filemtime($file)) > $maxAge) {
        unlink($file);
        $removed++;
    }
}

echo "Arquivos temporários removidos: {$removed}
";
