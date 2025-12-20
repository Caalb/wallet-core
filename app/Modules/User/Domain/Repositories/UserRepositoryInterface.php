<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Repositories;

use App\Modules\User\Domain\Entity\User;
use App\Modules\User\Domain\ValueObject\Email;

interface UserRepositoryInterface
{
    public function findById(int $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function existsByEmail(Email $email): bool;

    public function save(User $user): void;

    public function delete(User $user): void;
}
