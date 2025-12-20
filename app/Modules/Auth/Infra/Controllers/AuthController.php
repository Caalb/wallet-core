<?php

declare(strict_types=1);

namespace App\Modules\Auth\Infra\Controllers;

use App\Modules\Auth\Domain\UseCases\LoginUserUseCase;
use App\Modules\Auth\Domain\UseCases\RegisterUserUseCase;
use App\Modules\Auth\Infra\Requests\LoginRequest;
use App\Modules\Auth\Infra\Requests\RegisterRequest;
use App\Shared\Infrastructure\Http\AbstractController;
use Psr\Http\Message\ResponseInterface;

class AuthController extends AbstractController
{
    public function __construct(
        private readonly RegisterUserUseCase $registerUserUseCase,
        private readonly LoginUserUseCase $loginUserUseCase,
    ) {
    }

    public function register(RegisterRequest $request): ResponseInterface
    {
        $result = $this->registerUserUseCase->execute($request->validated());

        return $this->response->json(
            $result->toArray(),
        )->withStatus(201);
    }

    public function login(LoginRequest $request): ResponseInterface
    {
        $validated = $request->validated();
        $result = $this->loginUserUseCase->execute($validated);

        return $this->response->json(
            $result->toArray(),
        );
    }
}
