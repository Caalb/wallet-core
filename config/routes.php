<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @see     https://www.hyperf.io
 *
 * @document https://hyperf.wiki
 *
 * @contact  group@hyperf.io
 *
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use App\Modules\Auth\Infra\Controllers\AuthController;
use App\Modules\Transaction\Infra\Controllers\TransferController;
use Hyperf\HttpServer\Router\Router;
use Hyperf\Validation\Middleware\ValidationMiddleware;

Router::get('/health', function () {
    return 'HEALTHY';
});

Router::addGroup('/api/auth', function () {
    Router::post('/register', [AuthController::class, 'register']);
    Router::post('/login', [AuthController::class, 'login']);
}, [
    'middleware' => [
        ValidationMiddleware::class,
    ],
]);

Router::addGroup('/api/v1', function () {
    Router::post('/transfer', [TransferController::class, 'transfer']);
}, [
    'middleware' => [
        ValidationMiddleware::class,
    ],
]);
