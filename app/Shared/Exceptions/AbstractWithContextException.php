<?php

declare(strict_types=1);

namespace App\Shared\Exceptions;

use Hyperf\Server\Exception\ServerException;
use Throwable;

abstract class AbstractWithContextException extends ServerException
{
    protected array $context = [];

    protected array $data = [];

    public function __construct(string $message, int $code, ?Throwable $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getData(): array
    {
        return $this->data;
    }
}
