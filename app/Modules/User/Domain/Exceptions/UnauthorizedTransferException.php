<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Exceptions;

use App\Shared\Exceptions\AbstractWithContextException;
use Swoole\Http\Status;

class UnauthorizedTransferException extends AbstractWithContextException
{
    public function __construct(int $userId = 0, string $userType = '')
    {
        $message = 'unauthorized_transfer';
        $code = Status::FORBIDDEN;

        parent::__construct($message, $code);

        if ($userId > 0) {
            $this->data = [
                'user_id' => $userId,
                'user_type' => $userType,
            ];
        }
    }
}
