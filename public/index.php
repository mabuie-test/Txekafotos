<?php

declare(strict_types=1);

use App\Core\App;

require_once dirname(__DIR__) . '/bootstrap.php';

$app = new App(dirname(__DIR__));
$app->bootstrap();
$app->run();
