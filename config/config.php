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

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

use function Hyperf\Support\env;

return [
    'app_name' => env('APP_NAME', 'wallet-core'),
    'app_env' => env('APP_ENV', 'dev'),
    'scan_cacheable' => env('SCAN_CACHEABLE', false),
    'jwt_secret' => env('JWT_SECRET', 'x9438ugxhxpw94utpnv03fqqKaptCKdeLqorp39utkxgiNraSecretKeyForJWT2024'),
    'jwt_expiration_time' => (int) env('JWT_EXPIRATION_TIME', 86400),
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
];
