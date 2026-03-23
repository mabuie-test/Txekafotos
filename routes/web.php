<?php

declare(strict_types=1);

use App\Controllers\FeedbackController;
use App\Controllers\MediaController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\PaymentController;
use App\Controllers\RevisionController;
use App\Controllers\TrackingController;
use App\Core\App;
use App\Middleware\CsrfMiddleware;

$router = App::getInstance()->router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/media', [MediaController::class, 'show']);
$router->get('/pedido/criar', [OrderController::class, 'create']);
$router->post('/pedido', [OrderController::class, 'store'], [CsrfMiddleware::class]);
$router->get('/pedido/{id}/pagamento', [PaymentController::class, 'show']);
$router->post('/pedido/{id}/iniciar-pagamento', [PaymentController::class, 'initiate'], [CsrfMiddleware::class]);
$router->get('/acompanhar', [TrackingController::class, 'form']);
$router->post('/acompanhar', [TrackingController::class, 'lookup'], [CsrfMiddleware::class]);
$router->get('/pedido/{id}/status', [TrackingController::class, 'status']);
$router->post('/pedido/{id}/aprovar', [TrackingController::class, 'approve'], [CsrfMiddleware::class]);
$router->post('/pedido/{id}/revisao', [RevisionController::class, 'store'], [CsrfMiddleware::class]);
$router->post('/pedido/{id}/feedback', [FeedbackController::class, 'store'], [CsrfMiddleware::class]);
