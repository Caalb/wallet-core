<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\UseCases;

use App\Modules\Auth\Domain\Exceptions\UserNotFoundException;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class ChangePasswordUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function execute(int $userId, string $newPassword): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $user->setPassword($hashedPassword);
        $this->userRepository->save($user);
    }
}
