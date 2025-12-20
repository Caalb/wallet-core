<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\UseCases;

use App\Modules\Auth\Domain\Exceptions\UserNotFoundException;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class UpdateProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function execute(int $userId, string $name): void
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        $user->updateProfile($name);
        $this->userRepository->save($user);
    }
}
