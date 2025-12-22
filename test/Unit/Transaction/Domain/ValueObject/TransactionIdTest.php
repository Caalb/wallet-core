<?php

declare(strict_types=1);

namespace HyperfTest\Unit\Transaction\Domain\ValueObject;

use App\Modules\Transaction\Domain\ValueObject\TransactionId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class TransactionIdTest extends TestCase
{
    public function testShouldGenerateValidUuid(): void
    {
        $transactionId = TransactionId::generate();

        $this->assertInstanceOf(TransactionId::class, $transactionId);
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $transactionId->toString(),
        );
    }

    public function testShouldCreateFromValidString(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $transactionId = TransactionId::fromString($uuid);

        $this->assertInstanceOf(TransactionId::class, $transactionId);
        $this->assertEquals($uuid, $transactionId->toString());
        $this->assertEquals($uuid, (string) $transactionId);
    }

    public function testShouldCompareEquality(): void
    {
        $uuid = '550e8400-e29b-41d4-a716-446655440000';
        $transactionId1 = TransactionId::fromString($uuid);
        $transactionId2 = TransactionId::fromString($uuid);
        $transactionId3 = TransactionId::generate();

        $this->assertTrue($transactionId1->equals($transactionId2));
        $this->assertFalse($transactionId1->equals($transactionId3));
    }

    public function testShouldThrowExceptionForInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TransactionId::fromString('invalid-uuid');
    }
}
