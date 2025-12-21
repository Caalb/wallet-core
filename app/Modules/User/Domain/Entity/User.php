<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Entity;

use App\Modules\User\Domain\Enum\UserType;
use App\Modules\User\Domain\Exceptions\UnauthorizedTransferException;
use App\Modules\User\Domain\ValueObject\Email;
use DateTimeImmutable;

class User
{
    private int $id;

    private string $name;

    private Email $email;

    private string $password;

    private UserType $type;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $id,
        string $name,
        Email $email,
        string $password,
        UserType $type,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->type = $type;
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
        $this->updatedAt = $updatedAt ?? new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getType(): UserType
    {
        return $this->type;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isCommon(): bool
    {
        return $this->type->isCommon();
    }

    public function isMerchant(): bool
    {
        return $this->type->isMerchant();
    }

    /**
     * @throws UnauthorizedTransferException
     */
    public function validateCanTransfer(): void
    {
        if ($this->type === UserType::MERCHANT) {
            throw new UnauthorizedTransferException();
        }
    }

    public function updateProfile(string $name): void
    {
        $this->name = $name;
        $this->updatedAt = new DateTimeImmutable();
    }

    public function setPassword(string $hashedPassword): void
    {
        $this->password = $hashedPassword;
        $this->updatedAt = new DateTimeImmutable();
    }
}
