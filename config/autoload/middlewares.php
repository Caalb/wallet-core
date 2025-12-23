<?php

declare(strict_types=1);

use App\Shared\Infrastructure\Middleware\CorsMiddleware;

return [
    'http' => [
        CorsMiddleware::class,
    ],
];
