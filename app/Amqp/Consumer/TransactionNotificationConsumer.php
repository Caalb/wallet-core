<?php

declare(strict_types=1);

namespace App\Amqp\Consumer;

use Hyperf\Amqp\Annotation\Consumer;
use Hyperf\Amqp\Message\ConsumerMessage;
use Hyperf\Amqp\Result;
use Hyperf\Guzzle\ClientFactory;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Throwable;

use function Hyperf\Config\config;

#[Consumer(
    exchange: 'notifications',
    routingKey: 'transaction.notification',
    queue: 'transaction_notifications',
    name: 'TransactionNotificationConsumer',
    nums: 0,
    enable: false,
    maxConsumption: 0,
)]
class TransactionNotificationConsumer extends ConsumerMessage
{
    private const NOTIFICATION_URL = 'https://util.devi.tools/api/v1/notify';
    private const QUEUE_NAME = 'transaction_notifications';

    public function __construct(
        private ClientFactory $clientFactory,
        private LoggerInterface $logger,
    ) {
    }

    public function consumeMessage($data, AMQPMessage $amqpMessage): Result
    {
        $headers = $amqpMessage->get('application_headers');
        $retryCount = 0;

        if ($headers) {
            $nativeData = $headers->getNativeData();
            $retryCount = $nativeData['x-retry-count'] ?? 0;
        }

        $transactionId = $data['transaction_id'] ?? 'unknown';

        try {
            $client = $this->clientFactory->create(['timeout' => 10]);

            $response = $client->post(self::NOTIFICATION_URL, [
                'json' => $data,
            ]);

            $this->logger->info('Notification sent successfully', [
                'transaction_id' => $transactionId,
                'retry_count' => $retryCount,
            ]);

            return Result::ACK;
        } catch (Throwable $e) {
            $this->logger->warning('Notification failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
                'retry_count' => $retryCount,
            ]);

            if ($retryCount >= config('transaction.rabbitmq.max_retries')) {
                $this->logger->error('Notification max retries exceeded, sending to DLQ', [
                    'transaction_id' => $transactionId,
                ]);

                return Result::DROP;
            }

            return $this->retryWithDelay($amqpMessage, $retryCount);
        }
    }

    public function getQueue(): string
    {
        return self::QUEUE_NAME;
    }

    public function isEnable(): bool
    {
        return true;
    }

    private function retryWithDelay(AMQPMessage $amqpMessage, int $retryCount): Result
    {
        $headers = $amqpMessage->get('application_headers');

        if ($headers) {
            $nativeData = $headers->getNativeData();
            $nativeData['x-retry-count'] = $retryCount + 1;
            $amqpMessage->set('application_headers', $nativeData);
        }

        return Result::REQUEUE;
    }
}
