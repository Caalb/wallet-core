<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Wallet\Domain\ValueObject;

use App\Modules\Wallet\Domain\Exceptions\InvalidMoneyException;
use App\Modules\Wallet\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    public function testShouldCreateFromAmount(): void
    {
        $money = Money::fromAmount(100.50);

        $this->assertEquals(100.50, $money->getAmount());
        $this->assertEquals(10050, $money->getAmountInCents());
    }

    public function testShouldCreateFromCents(): void
    {
        $money = Money::fromCents(10050);

        $this->assertEquals(100.50, $money->getAmount());
        $this->assertEquals(10050, $money->getAmountInCents());
    }

    public function testShouldNotAllowNegativeAmount(): void
    {
        $this->expectException(InvalidMoneyException::class);
        Money::fromAmount(-10.0);
    }

    public function testShouldNotAllowNegativeCents(): void
    {
        $this->expectException(InvalidMoneyException::class);
        Money::fromCents(-1000);
    }

    public function testShouldAddMoney(): void
    {
        $money1 = Money::fromAmount(100.0);
        $money2 = Money::fromAmount(50.0);

        $result = $money1->add($money2);

        $this->assertEquals(150.0, $result->getAmount());
        $this->assertEquals(15000, $result->getAmountInCents());
    }

    public function testShouldSubtractMoney(): void
    {
        $money1 = Money::fromAmount(100.0);
        $money2 = Money::fromAmount(50.0);

        $result = $money1->subtract($money2);

        $this->assertEquals(50.0, $result->getAmount());
        $this->assertEquals(5000, $result->getAmountInCents());
    }

    public function testShouldCompareGreaterThan(): void
    {
        $money1 = Money::fromAmount(100.0);
        $money2 = Money::fromAmount(50.0);

        $this->assertTrue($money1->isGreaterThan($money2));
        $this->assertFalse($money2->isGreaterThan($money1));
    }

    public function testShouldCompareGreaterThanOrEqual(): void
    {
        $money1 = Money::fromAmount(100.0);
        $money2 = Money::fromAmount(100.0);
        $money3 = Money::fromAmount(50.0);

        $this->assertTrue($money1->isGreaterThanOrEqual($money2));
        $this->assertTrue($money1->isGreaterThanOrEqual($money3));
        $this->assertFalse($money3->isGreaterThanOrEqual($money1));
    }

    public function testShouldCompareEquality(): void
    {
        $money1 = Money::fromAmount(100.0);
        $money2 = Money::fromAmount(100.0);
        $money3 = Money::fromAmount(50.0);

        $this->assertTrue($money1->equals($money2));
        $this->assertFalse($money1->equals($money3));
    }

    public function testShouldDetectZero(): void
    {
        $money1 = Money::fromCents(0);
        $money2 = Money::fromAmount(10.0);

        $this->assertTrue($money1->isZero());
        $this->assertFalse($money2->isZero());
    }

    public function testShouldHandleRounding(): void
    {
        $money = Money::fromAmount(100.555);

        $this->assertEquals(10056, $money->getAmountInCents());
        $this->assertEquals(100.56, $money->getAmount());
    }
}
