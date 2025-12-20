<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infra\Services;

use App\Modules\Auth\Domain\Services\TokenServiceInterface;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Hyperf\Contract\ConfigInterface;

class JwtService implements TokenServiceInterface
{
    private string $secretKey;

    private string $algorithm = 'HS256';

    private int $expirationTime;

    public function __construct(ConfigInterface $config)
    {
        $this->secretKey = $config->get('jwt_secret');
        $this->expirationTime = $config->get('jwt_expiration_time');
    }

    public function generateToken(int $userId, string $email, string $type): string
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + $this->expirationTime;

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'sub' => $userId,
            'email' => $email,
            'type' => $type,
        ];

        return JWT::encode($payload, $this->secretKey, $this->algorithm);
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode(
                $token,
                new Key(
                    $this->secretKey,
                    $this->algorithm,
                ),
            );

            return (array) $decoded;
        } catch (Exception $e) {
            return null;
        }
    }

    public function getUserIdFromToken(string $token): ?int
    {
        $payload = $this->validateToken($token);

        return $payload['sub'] ?? null;
    }
}
