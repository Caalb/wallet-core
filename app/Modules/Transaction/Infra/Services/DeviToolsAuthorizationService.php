<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Services;

use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Exceptions\AuthorizationServiceException;
use App\Modules\Transaction\Domain\Services\AuthorizationServiceInterface;
use Hyperf\Guzzle\ClientFactory;
use Hyperf\Retry\Annotation\Retry;
use Psr\Log\LoggerInterface;
use Throwable;

use function Hyperf\Config\config;

class DeviToolsAuthorizationService implements AuthorizationServiceInterface
{
    private const AUTHORIZATION_URL = 'https://util.devi.tools/api/v2/authorize';

    public function __construct(
        private ClientFactory $clientFactory,
        private LoggerInterface $logger,
    ) {
    }

    #[Retry(maxAttempts: 3, base: 100)]
    public function authorize(Transaction $transaction): bool
    {
        try {
            $client = $this->clientFactory->create([
                'timeout' => config('transaction.authorization.timeout'),
            ]);

            $response = $client->get(self::AUTHORIZATION_URL);
            $body = json_decode($response->getBody()->getContents(), true);

            $this->logger->info('Authorization response', [
                'transaction_id' => $transaction->getId(),
                'response' => $body,
            ]);

            return ($body['status'] ?? '') === 'success';
        } catch (Throwable $e) {
            $this->logger->error('Authorization service failed', [
                'transaction_id' => $transaction->getId(),
                'error' => $e->getMessage(),
            ]);

            throw new AuthorizationServiceException(
                "Failed to authorize transaction: {$e->getMessage()}",
                previous: $e,
            );
        }
    }
}
