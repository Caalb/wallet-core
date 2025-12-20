<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Entity;

use App\Modules\User\Domain\Enum\DocumentType;
use App\Modules\User\Domain\ValueObject\Document;
use DateTimeImmutable;

class UserDocument
{
    private int $id;

    private int $userId;

    private Document $document;

    private DocumentType $type;

    private DateTimeImmutable $createdAt;

    private DateTimeImmutable $updatedAt;

    public function __construct(
        int $id,
        int $userId,
        Document $document,
        DocumentType $type,
        ?DateTimeImmutable $createdAt = null,
        ?DateTimeImmutable $updatedAt = null,
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->document = $document;
        $this->type = $type;
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

    public function getDocument(): Document
    {
        return $this->document;
    }

    public function getType(): DocumentType
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
}
