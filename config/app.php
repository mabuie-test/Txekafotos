<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Txekafotos'),
    'env' => env('APP_ENV', 'production'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOL),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Africa/Maputo'),
    'currency' => env('APP_CURRENCY', 'MZN'),
    'base_price' => (float) env('APP_BASE_PRICE', 45),
    'session_name' => env('SESSION_NAME', 'txekafotos_session'),
];
