<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Services;

use App\Amqp\Producer\TransactionNotificationProducer;
use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Services\NotificationServiceInterface;
use Hyperf\Amqp\Producer;

use function Hyperf\Config\config;

class RabbitMQNotificationService implements NotificationServiceInterface
{
    public function __construct(
        private Producer $producer,
    ) {
    }

    public function notifyTransactionCompleted(Transaction $transaction): void
    {
        $data = [
            'transaction_id' => $transaction->getId(),
            'payer_id' => $transaction->getPayerId(),
            'payee_id' => $transaction->getPayeeId(),
            'amount' => $transaction->getAmount()->getAmount(),
            'timestamp' => time(),
        ];

        $priority = $this->calculatePriority($transaction);

        $message = new TransactionNotificationProducer($data, $priority);
        $message->setExchange('notifications');
        $message->setRoutingKey('transaction.notification');

        $this->producer->produce($message);
    }

    private function calculatePriority(Transaction $transaction): int
    {
        $config = config('transaction.notification.priority');
        $amountInCents = $transaction->getAmount()->getAmountInCents();

        if ($amountInCents >= $config['high_threshold']) {
            return $config['high_priority'];
        }
        if ($amountInCents >= $config['medium_threshold']) {
            return $config['medium_priority'];
        }

        return $config['normal_priority'];
    }
}
