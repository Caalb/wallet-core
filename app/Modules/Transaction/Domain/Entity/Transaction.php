<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Entity;

use App\Modules\Transaction\Domain\Enum\TransactionStatus;
use App\Modules\Transaction\Domain\Exceptions\InvalidTransactionStateException;
use App\Modules\Transaction\Domain\ValueObject\TransactionId;
use App\Modules\Wallet\Domain\ValueObject\Money;
use DateTimeImmutable;

class Transaction
{
    private DateTimeImmutable $createdAt;

    public function __construct(
        private TransactionId $id,
        private int $payerId,
        private int $payeeId,
        private Money $amount,
        private TransactionStatus $status = TransactionStatus::PENDING,
        private ?string $failureReason = null,
        private ?string $idempotencyKey = null,
        private ?DateTimeImmutable $completedAt = null,
        private ?DateTimeImmutable $failedAt = null,
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public static function create(
        int $payerId,
        int $payeeId,
        Money $amount,
        ?string $idempotencyKey = null,
    ): self {
        return new self(
            id: TransactionId::generate(),
            payerId: $payerId,
            payeeId: $payeeId,
            amount: $amount,
            status: TransactionStatus::PENDING,
            idempotencyKey: $idempotencyKey,
        );
    }

    // Getters

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getPayerId(): int
    {
        return $this->payerId;
    }

    public function getPayeeId(): int
    {
        return $this->payeeId;
    }

    public function getAmount(): Money
    {
        return $this->amount;
    }

    public function getStatus(): TransactionStatus
    {
        return $this->status;
    }

    public function getFailureReason(): ?string
    {
        return $this->failureReason;
    }

    public function getIdempotencyKey(): ?string
    {
        return $this->idempotencyKey;
    }

    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function getFailedAt(): ?DateTimeImmutable
    {
        return $this->failedAt;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function complete(): void
    {
        if ($this->status !== TransactionStatus::PENDING) {
            throw new InvalidTransactionStateException(
                $this->id->toString(),
                $this->status->value,
                'complete',
            );
        }

        $this->status = TransactionStatus::COMPLETED;
        $this->completedAt = new DateTimeImmutable();
    }

    public function fail(string $reason): void
    {
        $this->status = TransactionStatus::FAILED;
        $this->failureReason = $reason;
        $this->failedAt = new DateTimeImmutable();
    }

    public function isFinal(): bool
    {
        return $this->status->isFinal();
    }
}
