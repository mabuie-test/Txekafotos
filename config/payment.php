<?php

declare(strict_types=1);

return [
    'base_url' => rtrim(env('DEBITO_BASE_URL', 'https://my.debito.co.mz/api/v1'), '/'),
    'token' => env('DEBITO_TOKEN', ''),
    'wallet_id' => env('DEBITO_WALLET_ID', ''),
    'timeout' => (int) env('DEBITO_TIMEOUT', 30),
    'method' => 'mpesa',
];
