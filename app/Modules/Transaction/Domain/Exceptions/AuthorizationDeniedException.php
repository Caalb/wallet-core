<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class AuthorizationDeniedException extends AbstractWithContextException
{
    public function __construct(string $message = 'Transaction not authorized')
    {
        parent::__construct($message, Status::UNPROCESSABLE_ENTITY);
    }
}
