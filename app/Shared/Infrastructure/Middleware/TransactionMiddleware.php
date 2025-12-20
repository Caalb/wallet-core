<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Middleware;

use Hyperf\DbConnection\Db;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class TransactionMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return Db::transaction(function () use ($request, $handler) {
            return $handler->handle($request);
        });
    }
}
