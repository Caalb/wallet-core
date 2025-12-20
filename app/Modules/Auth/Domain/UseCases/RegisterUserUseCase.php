<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\UseCases;

use App\Modules\Auth\Domain\DTO\AuthenticationResponse;
use App\Modules\Auth\Domain\Events\UserRegistered;
use App\Modules\Auth\Domain\Exceptions\UserAlreadyExistsException;
use App\Modules\Auth\Domain\Services\TokenServiceInterface;
use App\Modules\User\Domain\Entity\User;
use App\Modules\User\Domain\Entity\UserDocument;
use App\Modules\User\Domain\Enum\DocumentType;
use App\Modules\User\Domain\Enum\UserType;
use App\Modules\User\Domain\Exceptions\InvalidCNPJException;
use App\Modules\User\Domain\Repositories\UserDocumentRepositoryInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Domain\ValueObject\CNPJ;
use App\Modules\User\Domain\ValueObject\CPF;
use App\Modules\User\Domain\ValueObject\Email;
use Hyperf\Event\EventDispatcher;

class RegisterUserUseCase
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserDocumentRepositoryInterface $userDocumentRepository,
        private readonly TokenServiceInterface $jwtService,
        private readonly EventDispatcher $eventDispatcher,
    ) {
    }

    /**
     * @throws UserAlreadyExistsException
     * @throws InvalidCNPJException
     */
    public function execute(array $userData): AuthenticationResponse
    {
        $documents = [];
        if (isset($userData['cpf'])) {
            $documents[] = new CPF($userData['cpf']);
        }
        if (isset($userData['cnpj'])) {
            $documents[] = new CNPJ($userData['cnpj']);
        }

        $email = new Email($userData['email']);

        foreach ($documents as $document) {
            if ($this->userDocumentRepository->existsByDocument($document->getValue())) {
                $documentField = $document instanceof CNPJ ? 'cnpj' : 'cpf';

                throw new UserAlreadyExistsException($documentField, $document->getValue());
            }
        }

        if ($this->userRepository->existsByEmail($email)) {
            throw new UserAlreadyExistsException('email', $email->getValue());
        }

        $hashedPassword = password_hash($userData['password'], PASSWORD_BCRYPT);
        $userType = isset($userData['type']) ? UserType::from($userData['type']) : UserType::COMMON;

        $user = new User(
            id: 0,
            name: $userData['name'],
            email: $email,
            password: $hashedPassword,
            type: $userType,
        );

        $this->userRepository->save($user);

        foreach ($documents as $document) {
            $documentType = $document instanceof CNPJ ? DocumentType::CNPJ : DocumentType::CPF;
            $userDocument = new UserDocument(
                id: 0,
                userId: $user->getId(),
                document: $document,
                type: $documentType,
            );

            $this->userDocumentRepository->save($userDocument);
        }

        $this->eventDispatcher->dispatch(new UserRegistered($user->getId()));

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
