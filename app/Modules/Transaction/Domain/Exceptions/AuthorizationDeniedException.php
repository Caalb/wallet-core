<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class AuthorizationDeniedException extends AbstractWithContextException
{
    public function __construct()
    {
        $message = 'transaction_not_authorized';
        $code = Status::UNPROCESSABLE_ENTITY;

        parent::__construct($message, $code);
    }
}
