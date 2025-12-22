<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Services;

interface TokenServiceInterface
{
    public function generateToken(int $userId, string $email, string $type): string;

    public function validateToken(string $token): ?array;

    public function getUserIdFromToken(string $token): ?int;
}
