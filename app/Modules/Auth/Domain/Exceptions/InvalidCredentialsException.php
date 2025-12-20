<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InvalidCredentialsException extends AbstractWithContextException
{
    public function __construct()
    {
        $message = 'invalid_credentials';
        $code = Status::UNAUTHORIZED;

        parent::__construct($message, $code);
    }
}
