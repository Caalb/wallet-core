<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\Repositories;

use App\Modules\Wallet\Domain\Entity\Wallet;

interface WalletRepositoryInterface
{
    public function findById(int $id): ?Wallet;

    public function findByUserId(int $userId): ?Wallet;

    public function findByUserIdForUpdate(int $userId): ?Wallet;

    public function save(Wallet $wallet): void;

    public function delete(Wallet $wallet): void;
}
