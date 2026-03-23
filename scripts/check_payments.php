<?php

declare(strict_types=1);

use App\Core\App;
use App\Services\PaymentService;

require dirname(__DIR__) . '/bootstrap.php';

$app = new App(dirname(__DIR__));
$app->bootstrap();

$service = new PaymentService();

foreach ($service->syncPendingTransactions() as $result) {
    echo sprintf(
        "Transação %s sincronizada com status %s%s",
        $result['reference'],
        $result['status'],
        PHP_EOL
    );
}
