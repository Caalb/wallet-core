<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTO\TransferRequest;
use App\Modules\Transaction\Application\DTO\TransferResponse;
use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Exceptions\AuthorizationDeniedException;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\Services\AuthorizationServiceInterface;
use App\Modules\Transaction\Domain\Services\NotificationServiceInterface;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use App\Modules\Wallet\Domain\ValueObject\Money;
use Hyperf\DbConnection\Annotation\Transactional;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Throwable;

class ProcessTransferUseCase
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository,
        private UserRepositoryInterface $userRepository,
        private WalletRepositoryInterface $walletRepository,
        private AuthorizationServiceInterface $authorizationService,
        private NotificationServiceInterface $notificationService,
        private LoggerInterface $logger,
    ) {
    }

    public function execute(TransferRequest $request): TransferResponse
    {
        $existing = $this->transactionRepository->findByIdempotencyKey($request->idempotencyKey);

        if ($existing) {
            $this->logger->info('Idempotent request detected', [
                'idempotency_key' => $request->idempotencyKey,
                'transaction_id' => $existing->getId(),
            ]);

            return new TransferResponse(
                transactionId: $existing->getId(),
                payerId: $existing->getPayerId(),
                payeeId: $existing->getPayeeId(),
                amount: $existing->getAmount()->getAmount(),
                status: $existing->getStatus(),
                message: 'Transaction already processed',
                fromCache: true,
            );
        }

        $payer = $this->userRepository->findById($request->payerId);

        $payee = $this->userRepository->findById($request->payeeId);

        if (!$payer || !$payee) {
            throw new InvalidArgumentException('Payer or Payee not found');
        }

        $payer->validateCanTransfer();

        $money = Money::fromAmount($request->value);
        $transaction = Transaction::create(
            payerId: $request->payerId,
            payeeId: $request->payeeId,
            amount: $money,
            idempotencyKey: $request->idempotencyKey,
        );

        $this->transactionRepository->save($transaction);

        try {
            $authorized = $this->authorizationService->authorize($transaction);

            if (!$authorized) {
                $transaction->fail('Authorization denied by external service');
                $this->transactionRepository->save($transaction);

                throw new AuthorizationDeniedException('Transaction not authorized');
            }
        } catch (Throwable $e) {
            $transaction->fail('Authorization service error: ' . $e->getMessage());
            $this->transactionRepository->save($transaction);

            throw $e;
        }

        try {
            $this->processTransfer($transaction);
        } catch (Throwable $e) {
            $transaction->fail('Transfer failed: ' . $e->getMessage());
            $this->transactionRepository->save($transaction);

            throw $e;
        }

        $this->notificationService->notifyTransactionCompleted($transaction);

        return new TransferResponse(
            transactionId: $transaction->getId(),
            payerId: $transaction->getPayerId(),
            payeeId: $transaction->getPayeeId(),
            amount: $transaction->getAmount()->getAmount(),
            status: $transaction->getStatus(),
            message: 'Transfer completed successfully',
            fromCache: false,
        );
    }

    #[Transactional]
    private function processTransfer(Transaction $transaction): void
    {
        $wallets = $this->walletRepository->findByUserIdsWithLock([
            $transaction->getPayerId(),
            $transaction->getPayeeId(),
        ]);

        $payerWallet = $wallets[$transaction->getPayerId()] ?? null;
        $payeeWallet = $wallets[$transaction->getPayeeId()] ?? null;

        if (!$payerWallet || !$payeeWallet) {
            throw new RuntimeException('Wallet not found');
        }

        $payerWallet->debit($transaction->getAmount());

        $payeeWallet->credit($transaction->getAmount());

        $this->walletRepository->save($payerWallet);
        $this->walletRepository->save($payeeWallet);

        $transaction->complete();
        $this->transactionRepository->save($transaction);

        $this->logger->info('Transfer processed successfully', [
            'transaction_id' => $transaction->getId(),
            'payer_id' => $transaction->getPayerId(),
            'payee_id' => $transaction->getPayeeId(),
            'amount_cents' => $transaction->getAmount()->getAmountInCents(),
        ]);
    }
}
