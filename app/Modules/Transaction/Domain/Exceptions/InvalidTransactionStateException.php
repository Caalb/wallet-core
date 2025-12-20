<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class InvalidTransactionStateException extends AbstractWithContextException
{
    public function __construct(string $transactionId = '', string $currentState = '', string $attemptedAction = '')
    {
        $message = 'invalid_transaction_state';
        $code = Status::UNPROCESSABLE_ENTITY;

        parent::__construct($message, $code);

        if ($transactionId !== '') {
            $this->data = [
                'transaction_id' => $transactionId,
                'current_state' => $currentState,
                'attempted_action' => $attemptedAction,
            ];
        }
    }
}
