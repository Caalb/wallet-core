<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Repositories;

use App\Modules\User\Domain\Entity\UserDocument;

interface UserDocumentRepositoryInterface
{
    public function findByUserId(int $userId): array;

    public function findByDocument(string $document): ?UserDocument;

    public function existsByDocument(string $document): bool;

    public function save(UserDocument $userDocument): void;

    public function deleteByUserId(int $userId): void;
}
