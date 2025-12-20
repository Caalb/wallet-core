<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class UserNotFoundException extends AbstractWithContextException
{
    public function __construct(string $identifier = '')
    {
        $message = 'user_not_found';
        $code = Status::NOT_FOUND;

        parent::__construct($message, $code);

        if ($identifier !== '') {
            $this->data = [
                'identifier' => $identifier,
            ];
        }
    }
}
