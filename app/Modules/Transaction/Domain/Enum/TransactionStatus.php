<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Enum;

enum TransactionStatus: string
{
    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';

    public function isFinal(): bool
    {
        return $this === self::COMPLETED || $this === self::FAILED;
    }
}
