<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InvalidEmailException extends AbstractWithContextException
{
    public function __construct(string $email = '')
    {
        $message = 'invalid_email';
        $code = Status::BAD_REQUEST;

        parent::__construct($message, $code);

        if ($email !== '') {
            $this->data = [
                'email' => $email,
            ];
        }
    }
}
