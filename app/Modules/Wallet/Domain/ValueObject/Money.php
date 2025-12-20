<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\ValueObject;

use App\Modules\Wallet\Domain\Exceptions\InvalidMoneyException;
use JsonSerializable;

final readonly class Money implements JsonSerializable
{
    private int $amountInCents;

    /**
     * @throws InvalidMoneyException
     */
    private function __construct(int $amountInCents)
    {
        if ($amountInCents < 0) {
            throw new InvalidMoneyException('negative_cents');
        }

        $this->amountInCents = $amountInCents;
    }

    public function __toString(): string
    {
        return number_format($this->getAmount(), 2, '.', '');
    }

    /**
     * @throws InvalidMoneyException
     */
    public static function fromAmount(float $amount): self
    {
        if ($amount < 0) {
            throw new InvalidMoneyException('negative_amount');
        }

        return new self((int) round($amount * 100));
    }

    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function getAmount(): float
    {
        return $this->amountInCents / 100;
    }

    public function getAmountInCents(): int
    {
        return $this->amountInCents;
    }

    public function add(Money $other): Money
    {
        return self::fromCents($this->amountInCents + $other->amountInCents);
    }

    public function subtract(Money $other): Money
    {
        $result = $this->amountInCents - $other->amountInCents;

        return self::fromCents($result);
    }

    public function isGreaterThan(Money $other): bool
    {
        return $this->amountInCents > $other->amountInCents;
    }

    public function isGreaterThanOrEqual(Money $other): bool
    {
        return $this->amountInCents >= $other->amountInCents;
    }

    public function equals(Money $other): bool
    {
        return $this->amountInCents === $other->amountInCents;
    }

    public function isZero(): bool
    {
        return $this->amountInCents === 0;
    }

    public function jsonSerialize(): float
    {
        return $this->getAmount();
    }
}
