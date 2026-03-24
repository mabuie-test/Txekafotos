<?php

declare(strict_types=1);

use App\Core\App;
use App\Services\FinanceService;
use App\Services\ReportService;

require dirname(__DIR__) . '/bootstrap.php';

$app = new App(dirname(__DIR__));
$app->bootstrap();

$finance = new FinanceService();
$reports = new ReportService();

$payload = [
    'generated_at' => date('Y-m-d H:i:s'),
    'revenue_today' => $finance->getRevenueToday(),
    'revenue_month' => $finance->getRevenueThisMonth(),
    'payment_conversion_rate' => $reports->paymentConversionRate(),
    'satisfaction_rate' => $reports->satisfactionRate(),
    'orders_by_status' => $reports->ordersByStatus(),
];

$file = storage_path('logs/daily-metrics-' . date('Y-m-d') . '.json');
file_put_contents($file, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
echo "Métricas diárias guardadas em {$file}
";
