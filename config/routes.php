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

use App\Controller\IndexController;
use App\Modules\Auth\Infra\Controllers\AuthController;
use App\Shared\Infrastructure\Middleware\TransactionMiddleware;
use Hyperf\HttpServer\Router\Router;
use Hyperf\Validation\Middleware\ValidationMiddleware;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', [IndexController::class, 'index']);

Router::get('/favicon.ico', function () {
    return '';
});

Router::addGroup('/api/auth', function () {
    Router::post('/register', [AuthController::class, 'register']);
    Router::post('/login', [AuthController::class, 'login']);
}, [
    'middleware' => [
        ValidationMiddleware::class,
        TransactionMiddleware::class,
    ],
]);
