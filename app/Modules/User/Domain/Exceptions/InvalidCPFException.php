<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InvalidCPFException extends AbstractWithContextException
{
    public function __construct(string $cpf = '')
    {
        $message = 'invalid_cpf';
        $code = Status::BAD_REQUEST;

        parent::__construct($message, $code);

        if ($cpf !== '') {
            $this->data = [
                'cpf' => $cpf,
            ];
        }
    }
}
