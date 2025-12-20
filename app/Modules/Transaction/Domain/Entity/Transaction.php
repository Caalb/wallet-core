<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Entity;

use App\Modules\Transaction\Domain\Enum\TransactionStatus;
use App\Modules\Transaction\Domain\Exceptions\InvalidTransactionStateException;
use App\Modules\Wallet\Domain\ValueObject\Money;
use DateTimeImmutable;
use Ramsey\Uuid\Uuid;

class Transaction
{
    private string $id;

    private int $payerId;

    private int $payeeId;

    private Money $amount;

    private TransactionStatus $status;

    private ?string $failureReason;

    private ?string $idempotencyKey;

    private ?DateTimeImmutable $completedAt;

    private ?DateTimeImmutable $failedAt;

    private DateTimeImmutable $createdAt;

    public function __construct(
        string $id,
        int $payerId,
        int $payeeId,
        Money $amount,
        TransactionStatus $status = TransactionStatus::PENDING,
        ?string $failureReason = null,
        ?string $idempotencyKey = null,
        ?DateTimeImmutable $completedAt = null,
        ?DateTimeImmutable $failedAt = null,
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->id = $id;
        $this->payerId = $payerId;
        $this->payeeId = $payeeId;
        $this->amount = $amount;
        $this->status = $status;
        $this->failureReason = $failureReason;
        $this->idempotencyKey = $idempotencyKey;
        $this->completedAt = $completedAt;
        $this->failedAt = $failedAt;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public static function create(
        int $payerId,
        int $payeeId,
        Money $amount,
        ?string $idempotencyKey = null,
    ): self {
        return new self(
            id: Uuid::uuid4()->toString(),
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
        return $this->id;
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
                $this->id,
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

    public function isCompleted(): bool
    {
        return $this->status === TransactionStatus::COMPLETED;
    }

    public function isFailed(): bool
    {
        return $this->status === TransactionStatus::FAILED;
    }

    public function isSelfTransfer(): bool
    {
        return $this->payerId === $this->payeeId;
    }
}
