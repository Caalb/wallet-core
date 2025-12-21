<?php

declare(strict_types=1);

namespace App\Amqp\Producer;

use Hyperf\Amqp\Builder\ExchangeBuilder;
use Hyperf\Amqp\Message\ProducerMessage;

use function Hyperf\Config\config;

class TransactionNotificationProducer extends ProducerMessage
{
    private int $messagePriority = 5;

    public function __construct(array $data, int $priority = 5)
    {
        $this->payload = $data;
        $this->messagePriority = $priority;
    }

    public function getExchangeBuilder(): ExchangeBuilder
    {
        $builder = new ExchangeBuilder();
        $builder->setExchange($this->exchange);
        $builder->setType('direct');
        $builder->setDurable(true);

        return $builder;
    }

    public function getProperties(): array
    {
        return [
            'delivery_mode' => 2,
            'priority' => $this->messagePriority,
            'application_headers' => [
                'x-retry-count' => ['I', 0],
                'x-max-retries' => ['I', config('transaction.rabbitmq.max_retries')],
            ],
        ];
    }
}
