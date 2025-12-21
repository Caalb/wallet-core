<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

#[Consumer(
    exchange: 'notifications_dlq',
    routingKey: 'notification.failed',
    queue: 'transaction_notifications_dlq',
    name: 'FailedNotificationConsumer',
    nums: 0,
    enable: false,
    maxConsumption: 0,
)]
class FailedNotificationConsumer extends ConsumerMessage
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function consumeMessage($data, AMQPMessage $message): Result
    {
        $this->logger->error('Notification permanently failed', [
            'transaction_id' => $data['transaction_id'] ?? 'unknown',
            'data' => $data,
        ]);

        return Result::ACK;
    }

    public function getQueue(): string
    {
        return 'transaction_notifications_dlq';
    }

    public function isEnable(): bool
    {
        return true;
    }
}
