<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class ValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response): ResponsePlusInterface
    {
        $this->stopPropagation();
        /** @var ValidationException $throwable */
        $errors = $throwable->errors();
        $firstError = reset($errors);
        $firstMessage = $firstError ? reset($firstError) : 'Validation failed';

        $body = json_encode([
            'message' => $firstMessage,
            'data' => $errors,
        ], JSON_UNESCAPED_UNICODE);

        return $response
            ->setStatus($throwable->status)
            ->addHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream($body));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof ValidationException;
    }
}
