<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Exceptions;

use RuntimeException;
use Throwable;

class AuthorizationServiceException extends RuntimeException
{
    public function __construct(string $message = 'Authorization service failed', ?Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}
