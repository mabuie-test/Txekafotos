<?php

declare(strict_types=1);

use App\Core\App;
use App\Models\Transaction;
use App\Services\PaymentService;

require dirname(__DIR__) . '/bootstrap.php';

$app = new App(dirname(__DIR__));
$app->bootstrap();

$service = new PaymentService();
$transactions = (new Transaction())->pending();

foreach ($transactions as $transaction) {
    echo sprintf("A verificar transação %s do pedido #%d...
", $transaction['debito_reference'], $transaction['order_id']);
    try {
        $response = $service->syncOrderPaymentState($transaction['debito_reference']);
        echo 'Status atualizado: ' . json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
    } catch (Throwable $exception) {
        echo 'Erro: ' . $exception->getMessage() . PHP_EOL;
    }
}
