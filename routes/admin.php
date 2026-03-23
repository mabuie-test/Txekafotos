<?php

declare(strict_types=1);

use App\Controllers\AdminAuthController;
use App\Controllers\AdminDashboardController;
use App\Controllers\AdminFeedbackController;
use App\Controllers\AdminFinanceController;
use App\Controllers\AdminMarketingController;
use App\Controllers\AdminOrderController;
use App\Controllers\AdminRevisionController;
use App\Controllers\AdminShowcaseController;
use App\Core\App;
use App\Middleware\AdminMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\GuestMiddleware;

$router = App::getInstance()->router();

$router->get('/admin/login', [AdminAuthController::class, 'showLogin'], [GuestMiddleware::class]);
$router->post('/admin/login', [AdminAuthController::class, 'login'], [GuestMiddleware::class, CsrfMiddleware::class]);
$router->post('/admin/logout', [AdminAuthController::class, 'logout'], [AdminMiddleware::class, CsrfMiddleware::class]);

$router->group(['prefix' => '/admin', 'middleware' => [AdminMiddleware::class]], function ($router) {
    $router->get('/', [AdminDashboardController::class, 'index']);
    $router->get('/pedidos', [AdminOrderController::class, 'index']);
    $router->get('/pedidos/{id}', [AdminOrderController::class, 'show']);
    $router->post('/pedidos/{id}/status', [AdminOrderController::class, 'updateStatus'], [CsrfMiddleware::class]);
    $router->post('/pedidos/{id}/upload-final', [AdminOrderController::class, 'uploadFinal'], [CsrfMiddleware::class]);
    $router->get('/revisoes', [AdminRevisionController::class, 'index']);
    $router->post('/revisoes/{id}/responder', [AdminRevisionController::class, 'respond'], [CsrfMiddleware::class]);
    $router->get('/financeiro', [AdminFinanceController::class, 'index']);
    $router->get('/relatorios', [AdminFinanceController::class, 'reports']);
    $router->get('/showcases', [AdminShowcaseController::class, 'index']);
    $router->post('/showcases', [AdminShowcaseController::class, 'store'], [CsrfMiddleware::class]);
    $router->post('/showcases/{id}/toggle', [AdminShowcaseController::class, 'toggle'], [CsrfMiddleware::class]);
    $router->get('/feedbacks', [AdminFeedbackController::class, 'index']);
    $router->post('/feedbacks/{id}/toggle-publicacao', [AdminFeedbackController::class, 'togglePublication'], [CsrfMiddleware::class]);
    $router->get('/marketing', [AdminMarketingController::class, 'index']);
    $router->post('/marketing/homepage', [AdminMarketingController::class, 'updateHomepage'], [CsrfMiddleware::class]);
});
