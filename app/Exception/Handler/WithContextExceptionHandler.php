<?php

declare(strict_types=1);

namespace App\Exception\Handler;

use App\Shared\Exceptions\AbstractWithContextException;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Swow\Psr7\Message\ResponsePlusInterface;
use Throwable;

class WithContextExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponsePlusInterface $response): ResponsePlusInterface
    {
        $this->stopPropagation();
        /** @var AbstractWithContextException $throwable */
        $data = $throwable->getData();
        $body = json_encode([
            'message' => $throwable->getMessage(),
            'data' => !empty($data) ? $data : null,
        ], JSON_UNESCAPED_UNICODE);

        return $response
            ->setStatus($throwable->getCode())
            ->addHeader('content-type', 'application/json; charset=utf-8')
            ->setBody(new SwooleStream($body));
    }

    public function isValid(Throwable $throwable): bool
    {
        return $throwable instanceof AbstractWithContextException;
    }
}
