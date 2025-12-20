<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\UseCases;

use App\Modules\Auth\Domain\DTO\AuthenticationResponse;
use App\Modules\Auth\Domain\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Domain\Services\TokenServiceInterface;
use App\Modules\User\Domain\Exceptions\InvalidEmailException;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Domain\ValueObject\Email;

readonly class LoginUserUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private TokenServiceInterface $jwtService,
    ) {
    }

    /**
     * @throws InvalidCredentialsException
     * @throws InvalidEmailException
     */
    public function execute(array $data): AuthenticationResponse
    {
        $email = new Email($data['email']);

        $user = $this->userRepository->findByEmail($email);

        if (!$user) {
            throw new InvalidCredentialsException();
        }

        if (!password_verify($data['password'], $user->getPassword())) {
            throw new InvalidCredentialsException();
        }

        $token = $this->jwtService->generateToken(
            $user->getId(),
            $user->getEmail()->getValue(),
            $user->getType()->value,
        );

        return new AuthenticationResponse(
            userId: $user->getId(),
            name: $user->getName(),
            email: $user->getEmail()->getValue(),
            type: $user->getType()->value,
            token: $token,
        );
    }
}
