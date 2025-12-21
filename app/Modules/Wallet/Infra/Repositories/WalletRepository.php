<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Infra\Repositories;

use App\Modules\Wallet\Domain\Entity\Wallet;
use App\Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use App\Modules\Wallet\Domain\ValueObject\Money;
use App\Modules\Wallet\Infra\Models\WalletModel;
use ReflectionClass;

class WalletRepository implements WalletRepositoryInterface
{
    public function findById(int $id): ?Wallet
    {
        $model = WalletModel::query()->find($id);

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function findByUserId(int $userId): ?Wallet
    {
        $model = WalletModel::query()
            ->where('user_id', $userId)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function findByUserIdForUpdate(int $userId): ?Wallet
    {
        $model = WalletModel::query()
            ->where('user_id', $userId)
            ->lockForUpdate()
            ->first();

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function save(Wallet $wallet): void
    {
        $model = WalletModel::query()->firstOrNew(['id' => $wallet->getId()]);

        $model->user_id = $wallet->getUserId();
        $model->balance_cents = $wallet->getBalance()->getAmountInCents();
        $model->save();

        if ($wallet->getId() === 0) {
            $reflection = new ReflectionClass($wallet);
            $property = $reflection->getProperty('id');
            $property->setValue($wallet, $model->id);
        }
    }

    public function delete(Wallet $wallet): void
    {
        WalletModel::query()
            ->where('id', $wallet->getId())
            ->delete();
    }

    public function lockForUpdate(int $id): ?Wallet
    {
        $model = WalletModel::query()
            ->where('id', $id)
            ->lockForUpdate()
            ->first();

        if (!$model) {
            return null;
        }

        return $this->modelToEntity($model);
    }

    public function findByUserIdsWithLock(array $userIds): array
    {
        sort($userIds);

        $models = WalletModel::query()
            ->whereIn('user_id', $userIds)
            ->lockForUpdate()
            ->get();

        $wallets = [];
        foreach ($models as $model) {
            $wallets[$model->user_id] = $this->modelToEntity($model);
        }

        return $wallets;
    }

    private function modelToEntity(WalletModel $model): Wallet
    {
        return new Wallet(
            id: $model->id,
            userId: $model->user_id,
            balance: Money::fromCents($model->balance_cents),
            createdAt: $model->created_at->toDateTimeImmutable(),
            updatedAt: $model->updated_at->toDateTimeImmutable(),
        );
    }
}
