<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Services;

use App\Modules\Transaction\Domain\Entity\Transaction;

interface NotificationServiceInterface
{
    public function notifyTransactionCompleted(Transaction $transaction): void;
}
