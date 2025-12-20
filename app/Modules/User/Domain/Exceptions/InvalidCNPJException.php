<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InvalidCNPJException extends AbstractWithContextException
{
    public function __construct(string $cnpj = '')
    {
        $message = 'invalid_cnpj';
        $code = Status::BAD_REQUEST;

        parent::__construct($message, $code);

        if ($cnpj !== '') {
            $this->data = [
                'cnpj' => $cnpj,
            ];
        }
    }
}
