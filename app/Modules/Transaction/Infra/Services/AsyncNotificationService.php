<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Services;

use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Services\NotificationServiceInterface;
use App\Modules\Transaction\Infra\Jobs\SendTransactionNotificationJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;

class AsyncNotificationService implements NotificationServiceInterface
{
    public function __construct(
        private DriverFactory $driverFactory,
    ) {
    }

    public function notifyTransactionCompleted(Transaction $transaction): void
    {
        $driver = $this->driverFactory->get('default');

        $driver->push(new SendTransactionNotificationJob(
            transactionId: $transaction->getId(),
            payerId: $transaction->getPayerId(),
            payeeId: $transaction->getPayeeId(),
            amount: $transaction->getAmount()->getAmount(),
        ), delay: 0);
    }
}
