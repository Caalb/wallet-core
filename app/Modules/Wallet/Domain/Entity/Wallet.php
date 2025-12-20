<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\Entity;

use App\Modules\Wallet\Domain\Exceptions\InsufficientFundsException;
use App\Modules\Wallet\Domain\ValueObject\Money;
use DateTimeImmutable;

class Wallet
{
    private int $id;

    private int $userId;

    private Money $balance;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $id,
        int $userId,
        Money $balance,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->balance = $balance;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBalance(): Money
    {
        return $this->balance;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function hasSufficientFunds(Money $amount): bool
    {
        return $this->balance->isGreaterThanOrEqual($amount);
    }

    /**
     * @throws InsufficientFundsException
     */
    public function debit(Money $amount): void
    {
        if (!$this->hasSufficientFunds($amount)) {
            throw new InsufficientFundsException(
                $this->balance->getAmountInCents(),
                $amount->getAmountInCents(),
            );
        }

        $this->balance = $this->balance->subtract($amount);
        $this->updatedAt = new DateTimeImmutable();
    }

    public function credit(Money $amount): void
    {
        $this->balance = $this->balance->add($amount);
        $this->updatedAt = new DateTimeImmutable();
    }
}
