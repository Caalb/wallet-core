<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InvalidMoneyException extends AbstractWithContextException
{
    public function __construct(string $reason = '')
    {
        $message = 'invalid_money';
        $code = Status::BAD_REQUEST;

        parent::__construct($message, $code);

        if ($reason !== '') {
            $this->data = [
                'reason' => $reason,
            ];
        }
    }
}
