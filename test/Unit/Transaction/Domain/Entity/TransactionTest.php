<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Transaction\Domain\Entity;

use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Enum\TransactionStatus;
use App\Modules\Transaction\Domain\Exceptions\InvalidTransactionStateException;
use App\Modules\Wallet\Domain\ValueObject\Money;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class TransactionTest extends TestCase
{
    public function testShouldCreateTransaction(): void
    {
        $payerId = 1;
        $payeeId = 2;
        $amount = Money::fromAmount(100.0);
        $idempotencyKey = 'unique-key-123';

        $transaction = Transaction::create($payerId, $payeeId, $amount, $idempotencyKey);

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertEquals($payerId, $transaction->getPayerId());
        $this->assertEquals($payeeId, $transaction->getPayeeId());
        $this->assertTrue($amount->equals($transaction->getAmount()));
        $this->assertEquals(TransactionStatus::PENDING, $transaction->getStatus());
        $this->assertEquals($idempotencyKey, $transaction->getIdempotencyKey());
        $this->assertNull($transaction->getFailureReason());
        $this->assertNull($transaction->getCompletedAt());
        $this->assertNull($transaction->getFailedAt());
        $this->assertInstanceOf(DateTimeImmutable::class, $transaction->getCreatedAt());
    }

    public function testShouldCompleteTransaction(): void
    {
        $transaction = Transaction::create(1, 2, Money::fromAmount(100.0));

        $transaction->complete();

        $this->assertEquals(TransactionStatus::COMPLETED, $transaction->getStatus());
        $this->assertInstanceOf(DateTimeImmutable::class, $transaction->getCompletedAt());
        $this->assertTrue($transaction->isFinal());
    }

    public function testShouldFailTransaction(): void
    {
        $transaction = Transaction::create(1, 2, Money::fromAmount(100.0));
        $reason = 'Authorization denied';

        $transaction->fail($reason);

        $this->assertEquals(TransactionStatus::FAILED, $transaction->getStatus());
        $this->assertEquals($reason, $transaction->getFailureReason());
        $this->assertInstanceOf(DateTimeImmutable::class, $transaction->getFailedAt());
        $this->assertTrue($transaction->isFinal());
    }

    public function testShouldNotCompleteAlreadyCompletedTransaction(): void
    {
        $transaction = Transaction::create(1, 2, Money::fromAmount(100.0));
        $transaction->complete();

        $this->expectException(InvalidTransactionStateException::class);

        $transaction->complete();
    }

    public function testShouldNotCompleteFailedTransaction(): void
    {
        $transaction = Transaction::create(1, 2, Money::fromAmount(100.0));
        $transaction->fail('Some reason');

        $this->expectException(InvalidTransactionStateException::class);

        $transaction->complete();
    }
}
