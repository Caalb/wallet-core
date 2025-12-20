<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Infra\Listeners;

use App\Modules\Auth\Domain\Events\UserRegistered;
use App\Modules\Wallet\Domain\Entity\Wallet;
use App\Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use App\Modules\Wallet\Domain\ValueObject\Money;
use Hyperf\Event\Annotation\Listener;
use Hyperf\Event\Contract\ListenerInterface;

#[Listener]
class CreateWalletOnUserRegistered implements ListenerInterface
{
    public function __construct(
        private readonly WalletRepositoryInterface $walletRepository,
    ) {
    }

    public function listen(): array
    {
        return [
            UserRegistered::class,
        ];
    }

    public function process(object $event): void
    {
        /** @var UserRegistered $event */
        $wallet = new Wallet(
            id: 0,
            userId: $event->userId,
            balance: Money::fromCents(0),
        );

        $this->walletRepository->save($wallet);
    }
}
