<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InsufficientFundsException extends AbstractWithContextException
{
    public function __construct(int $available = 0, int $required = 0)
    {
        $message = 'insufficient_funds';
        $code = Status::UNPROCESSABLE_ENTITY;

        parent::__construct($message, $code);

        if ($available > 0 || $required > 0) {
            $this->data = [
                'available_cents' => $available,
                'required_cents' => $required,
            ];
        }
    }
}
