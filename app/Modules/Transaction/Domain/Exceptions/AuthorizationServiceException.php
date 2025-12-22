<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class AuthorizationServiceException extends AbstractWithContextException
{
    public function __construct()
    {
        $message = 'authorization_service_failed';
        $code = Status::INTERNAL_SERVER_ERROR;

        parent::__construct($message, $code);
    }
}
