<?php

declare(strict_types=1);

namespace HyperfTest\Integration\Transaction\Application\UseCases;

use App\Modules\Transaction\Application\DTO\TransferRequest;
use App\Modules\Transaction\Application\UseCases\ProcessTransferUseCase;
use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Enum\TransactionStatus;
use App\Modules\Transaction\Domain\Exceptions\AuthorizationDeniedException;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\Services\AuthorizationServiceInterface;
use App\Modules\Transaction\Domain\Services\NotificationServiceInterface;
use App\Modules\User\Domain\Entity\User;
use App\Modules\User\Domain\Enum\UserType;
use App\Modules\User\Domain\Exceptions\UnauthorizedTransferException;
use App\Modules\User\Domain\Repositories\UserRepositoryInterface;
use App\Modules\User\Domain\ValueObject\Email;
use App\Modules\Wallet\Domain\Entity\Wallet;
use App\Modules\Wallet\Domain\Exceptions\InsufficientFundsException;
use App\Modules\Wallet\Domain\Repositories\WalletRepositoryInterface;
use App\Modules\Wallet\Domain\ValueObject\Money;
use InvalidArgumentException;
use Mockery;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class ProcessTransferUseCaseTest extends TestCase
{
    private ProcessTransferUseCase $useCase;

    private TransactionRepositoryInterface $transactionRepository;

    private UserRepositoryInterface $userRepository;

    private WalletRepositoryInterface $walletRepository;

    private AuthorizationServiceInterface $authorizationService;

    private NotificationServiceInterface $notificationService;

    private LoggerInterface $logger;

    protected function setUp(): void
    {
        parent::setUp();

        $this->transactionRepository = Mockery::mock(TransactionRepositoryInterface::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->walletRepository = Mockery::mock(WalletRepositoryInterface::class);
        $this->authorizationService = Mockery::mock(AuthorizationServiceInterface::class);
        $this->notificationService = Mockery::mock(NotificationServiceInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);

        $this->useCase = new ProcessTransferUseCase(
            $this->transactionRepository,
            $this->userRepository,
            $this->walletRepository,
            $this->authorizationService,
            $this->notificationService,
            $this->logger,
        );

        $this->logger->allows('info')->andReturnNull();
        $this->logger->allows('error')->andReturnNull();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldProcessTransferSuccessfully(): void
    {
        $request = new TransferRequest(
            payerId: 1,
            payeeId: 2,
            value: 100.0,
            idempotencyKey: 'key-123',
        );

        $payer = $this->createUser(1, 'Chico', 'chico@gmail.com', UserType::COMMON);
        $payee = $this->createUser(2, 'Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);
        $payerWallet = $this->createWallet(1, 1, 200.0);
        $payeeWallet = $this->createWallet(2, 2, 50.0);

        $this->transactionRepository
            ->expects('findByIdempotencyKey')
            ->with('key-123')
            ->andReturn(null);

        $this->userRepository
            ->expects('findById')
            ->with(1)
            ->andReturn($payer);

        $this->userRepository
            ->expects('findById')
            ->with(2)
            ->andReturn($payee);

        $this->transactionRepository
            ->expects('save')
            ->atLeast()
            ->once();

        $this->authorizationService
            ->expects('authorize')
            ->andReturn(true);

        $this->walletRepository
            ->expects('findByUserIdsWithLock')
            ->with([1, 2])
            ->andReturn([
                1 => $payerWallet,
                2 => $payeeWallet,
            ]);

        $this->walletRepository
            ->expects('save')
            ->twice();

        $this->notificationService
            ->expects('notifyTransactionCompleted')
            ->once();

        $response = $this->useCase->execute($request);

        $this->assertNotNull($response->transactionId);
        $this->assertEquals(1, $response->payerId);
        $this->assertEquals(2, $response->payeeId);
        $this->assertEquals(100.0, $response->amount);
        $this->assertEquals(TransactionStatus::COMPLETED, $response->status);
        $this->assertEquals('Transfer completed successfully', $response->message);
        $this->assertFalse($response->fromCache);
    }

    public function testShouldReturnCachedResponseForIdempotentRequest(): void
    {
        $request = new TransferRequest(
            payerId: 1,
            payeeId: 2,
            value: 100.0,
            idempotencyKey: 'key-123',
        );

        $existingTransaction = Transaction::create(1, 2, Money::fromAmount(100.0), 'key-123');
        $existingTransaction->complete();

        $this->transactionRepository
            ->expects('findByIdempotencyKey')
            ->with('key-123')
            ->andReturn($existingTransaction);

        $response = $this->useCase->execute($request);

        $this->assertTrue($response->fromCache);
        $this->assertEquals('Transaction already processed', $response->message);
    }

    public function testShouldThrowExceptionWhenPayerNotFound(): void
    {
        $request = new TransferRequest(
            payerId: 999,
            payeeId: 2,
            value: 100.0,
            idempotencyKey: 'key-123',
        );

        $this->transactionRepository
            ->expects('findByIdempotencyKey')
            ->andReturn(null);

        $this->userRepository
            ->expects('findById')
            ->with(999)
            ->andReturn(null);

        $this->userRepository
            ->expects('findById')
            ->with(2)
            ->andReturn(null);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Payer or Payee not found');

        $this->useCase->execute($request);
    }

    public function testShouldThrowExceptionWhenMerchantTriesToTransfer(): void
    {
        $request = new TransferRequest(
            payerId: 1,
            payeeId: 2,
            value: 100.0,
            idempotencyKey: 'key-123',
        );

        $merchant = $this->createUser(1, 'Merchant', 'merchant@gmail.com', UserType::MERCHANT);
        $payee = $this->createUser(2, 'Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);

        $this->transactionRepository
            ->expects('findByIdempotencyKey')
            ->andReturn(null);

        $this->userRepository
            ->expects('findById')
            ->with(1)
            ->andReturn($merchant);

        $this->userRepository
            ->expects('findById')
            ->with(2)
            ->andReturn($payee);

        $this->expectException(UnauthorizedTransferException::class);

        $this->useCase->execute($request);
    }

    public function testShouldFailTransactionWhenAuthorizationDenied(): void
    {
        $request = new TransferRequest(
            payerId: 1,
            payeeId: 2,
            value: 100.0,
            idempotencyKey: 'key-123',
        );

        $payer = $this->createUser(1, 'Chico', 'chico@gmail.com', UserType::COMMON);
        $payee = $this->createUser(2, 'Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);

        $this->transactionRepository
            ->expects('findByIdempotencyKey')
            ->andReturn(null);

        $this->userRepository
            ->expects('findById')
            ->with(1)
            ->andReturn($payer);

        $this->userRepository
            ->expects('findById')
            ->with(2)
            ->andReturn($payee);

        $this->transactionRepository
            ->expects('save')
            ->atLeast()
            ->twice();

        $this->authorizationService
            ->expects('authorize')
            ->andReturn(false);

        $this->expectException(AuthorizationDeniedException::class);

        $this->useCase->execute($request);
    }

    public function testShouldFailTransactionWhenInsufficientFunds(): void
    {
        $request = new TransferRequest(
            payerId: 1,
            payeeId: 2,
            value: 100.0,
            idempotencyKey: 'key-123',
        );

        $payer = $this->createUser(1, 'Chico', 'chico@gmail.com', UserType::COMMON);
        $payee = $this->createUser(2, 'Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);
        $payerWallet = $this->createWallet(1, 1, 50.0); // Insufficient balance
        $payeeWallet = $this->createWallet(2, 2, 50.0);

        $this->transactionRepository
            ->expects('findByIdempotencyKey')
            ->andReturn(null);

        $this->userRepository
            ->expects('findById')
            ->with(1)
            ->andReturn($payer);

        $this->userRepository
            ->expects('findById')
            ->with(2)
            ->andReturn($payee);

        $this->transactionRepository
            ->expects('save')
            ->atLeast()
            ->twice();

        $this->authorizationService
            ->expects('authorize')
            ->andReturn(true);

        $this->walletRepository
            ->expects('findByUserIdsWithLock')
            ->with([1, 2])
            ->andReturn([
                1 => $payerWallet,
                2 => $payeeWallet,
            ]);

        $this->expectException(InsufficientFundsException::class);

        $this->useCase->execute($request);
    }

    private function createUser(int $id, string $name, string $email, UserType $type): User
    {
        return new User(
            $id,
            $name,
            new Email($email),
            'hashed_password',
            $type,
        );
    }

    private function createWallet(int $id, int $userId, float $balance): Wallet
    {
        return new Wallet(
            $id,
            $userId,
            Money::fromAmount($balance),
        );
    }
}
