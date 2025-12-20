<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Services;

interface TokenServiceInterface
{
    /**
     * Generate a JWT token for the authenticated user.
     */
    public function generateToken(int $userId, string $email, string $type): string;

    /**
     * Validate a JWT token and return its payload.
     *
     * @return null|array<string, mixed>
     */
    public function validateToken(string $token): ?array;

    /**
     * Extract the user ID from a JWT token.
     */
    public function getUserIdFromToken(string $token): ?int;
}
