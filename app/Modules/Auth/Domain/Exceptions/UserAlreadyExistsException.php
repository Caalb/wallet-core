<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class UserAlreadyExistsException extends AbstractWithContextException
{
    public function __construct(string $field = '', string $value = '')
    {
        $message = 'user_already_exists';
        $code = Status::CONFLICT;

        parent::__construct($message, $code);

        if ($field !== '' && $value !== '') {
            $this->data = [
                'field' => $field,
                'value' => $value,
            ];
        }
    }
}
