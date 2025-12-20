<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\UseCases;

use App\Modules\Auth\Domain\Exceptions\UserNotFoundException;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;

class GetProfileUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    /**
     * @throws UserNotFoundException
     */
    public function execute(int $userId): array
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException();
        }

        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail()->getValue(),
            'type' => $user->getType()->value,
            'created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $user->getUpdatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}
