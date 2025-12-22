<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Wallet\Domain\Entity;

use App\Modules\Wallet\Domain\Entity\Wallet;
use App\Modules\Wallet\Domain\Exceptions\InsufficientFundsException;
use App\Modules\Wallet\Domain\ValueObject\Money;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class WalletTest extends TestCase
{
    public function testShouldCreateWallet(): void
    {
        $balance = Money::fromAmount(100.0);
        $wallet = new Wallet(
            id: 1,
            userId: 1,
            balance: $balance,
        );

        $this->assertEquals(1, $wallet->getId());
        $this->assertEquals(1, $wallet->getUserId());
        $this->assertTrue($balance->equals($wallet->getBalance()));
        $this->assertInstanceOf(DateTimeImmutable::class, $wallet->getCreatedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $wallet->getUpdatedAt());
    }

    public function testShouldHaveSufficientFunds(): void
    {
        $wallet = new Wallet(
            id: 1,
            userId: 1,
            balance: Money::fromAmount(100.0),
        );

        $this->assertTrue($wallet->hasSufficientFunds(Money::fromAmount(50.0)));
        $this->assertTrue($wallet->hasSufficientFunds(Money::fromAmount(100.0)));
        $this->assertFalse($wallet->hasSufficientFunds(Money::fromAmount(150.0)));
    }

    public function testShouldDebitSuccessfully(): void
    {
        $wallet = new Wallet(
            id: 1,
            userId: 1,
            balance: Money::fromAmount(100.0),
        );

        $wallet->debit(Money::fromAmount(30.0));

        $this->assertEquals(70.0, $wallet->getBalance()->getAmount());
        $this->assertEquals(7000, $wallet->getBalance()->getAmountInCents());
        $this->assertInstanceOf(DateTimeImmutable::class, $wallet->getUpdatedAt());
    }

    public function testShouldThrowExceptionWhenDebitingMoreThanBalance(): void
    {
        $wallet = new Wallet(
            id: 1,
            userId: 1,
            balance: Money::fromAmount(50.0),
        );

        $this->expectException(InsufficientFundsException::class);

        $wallet->debit(Money::fromAmount(100.0));
    }

    public function testShouldCreditSuccessfully(): void
    {
        $wallet = new Wallet(
            id: 1,
            userId: 1,
            balance: Money::fromAmount(100.0),
        );

        $wallet->credit(Money::fromAmount(50.0));

        $this->assertEquals(150.0, $wallet->getBalance()->getAmount());
        $this->assertEquals(15000, $wallet->getBalance()->getAmountInCents());
        $this->assertInstanceOf(DateTimeImmutable::class, $wallet->getUpdatedAt());
    }

    public function testShouldPerformMultipleOperations(): void
    {
        $wallet = new Wallet(
            id: 1,
            userId: 1,
            balance: Money::fromAmount(100.0),
        );

        $wallet->debit(Money::fromAmount(20.0));
        $this->assertEquals(80.0, $wallet->getBalance()->getAmount());

        $wallet->credit(Money::fromAmount(50.0));
        $this->assertEquals(130.0, $wallet->getBalance()->getAmount());

        $wallet->debit(Money::fromAmount(30.0));
        $this->assertEquals(100.0, $wallet->getBalance()->getAmount());
    }
}
