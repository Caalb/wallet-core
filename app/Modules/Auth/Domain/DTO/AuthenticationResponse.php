<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\DTO;

final readonly class AuthenticationResponse
{
    public function __construct(
        public int $userId,
        public string $name,
        public string $email,
        public string $type,
        public string $token,
    ) {
    }

    public function toArray(): array
    {
        return [
            'user' => [
                'id' => $this->userId,
                'name' => $this->name,
                'email' => $this->email,
                'type' => $this->type,
            ],
            'token' => $this->token,
        ];
    }
}
