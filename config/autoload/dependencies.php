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

use App\Modules\Auth\Domain\Services\TokenServiceInterface;
use App\Modules\Auth\Infra\Services\JwtService;
use App\Modules\User\Domain\Repositories\UserDocumentRepositoryInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Infra\Repositories\UserDocumentRepository;
use App\Modules\User\Infra\Repositories\UserRepository;
use App\Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use App\Modules\Wallet\Infra\Repositories\WalletRepository;

return [
    UserRepositoryInterface::class => UserRepository::class,
    UserDocumentRepositoryInterface::class => UserDocumentRepository::class,
    WalletRepositoryInterface::class => WalletRepository::class,
    TokenServiceInterface::class => JwtService::class,
];
