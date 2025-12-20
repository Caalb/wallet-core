<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\UseCases;

use App\Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;

class GetBalanceUseCase
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
    ) {
    }

    public function execute(int $userId): array
    {
        $wallet = $this->walletRepository->findByUserId($userId);

        if (!$wallet) {
            return [
                'balance' => 0.0,
                'balance_cents' => 0,
            ];
        }

        return [
            'balance' => $wallet->getBalance()->getAmount(),
            'balance_cents' => $wallet->getBalance()->getAmountInCents(),
        ];
    }
}
