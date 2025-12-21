<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\ValueObject;

use JsonSerializable;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final readonly class TransactionId implements JsonSerializable
{
    private UuidInterface $uuid;

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public static function generate(): self
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): self
    {
        return new self(Uuid::fromString($id));
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function equals(TransactionId $other): bool
    {
        return $this->uuid->equals($other->uuid);
    }

    public function jsonSerialize(): string
    {
        return $this->toString();
    }
}
