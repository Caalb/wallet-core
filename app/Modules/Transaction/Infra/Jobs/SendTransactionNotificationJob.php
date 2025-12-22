<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Jobs;

use GuzzleHttp\Exception\GuzzleException;
use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Retry\Annotation\Retry;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Log\LoggerInterface;
use Throwable;

class SendTransactionNotificationJob extends Job
{
    private const NOTIFICATION_URL = 'https://util.devi.tools/api/v1/notify';

    private const TIMEOUT = 10;

    public function __construct(
        public string $transactionId,
        public int $payerId,
        public int $payeeId,
        public float $amount,
    ) {
    }

    /**
     * @throws Throwable
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws GuzzleException
     */
    #[Retry(maxAttempts: 5, base: 1000)]
    public function handle(): void
    {
        $container = ApplicationContext::getContainer();
        $client = $container->get(ClientFactory::class)->create([
            'timeout' => self::TIMEOUT,
        ]);

        $logger = $container->get(LoggerInterface::class);

        try {
            $response = $client->post(self::NOTIFICATION_URL, [
                'json' => [
                    'transaction_id' => $this->transactionId,
                    'payer_id' => $this->payerId,
                    'payee_id' => $this->payeeId,
                    'amount' => $this->amount,
                ],
            ]);

            $logger->info('Notification sent', [
                'transaction_id' => $this->transactionId,
            ]);
        } catch (Throwable $e) {
            $logger->error('Notification failed', [
                'transaction_id' => $this->transactionId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
