<?php

declare(strict_types=1);

namespace HyperfTest\Feature\Transaction;

use App\Modules\Transaction\Domain\Services\AuthorizationServiceInterface;
use App\Modules\User\Domain\Enum\UserType;
use App\Modules\User\Infra\Models\UserModel;
use App\Modules\Wallet\Infra\Models\WalletModel;
use Hyperf\Context\ApplicationContext;
use HyperfTest\HttpTestCase;
use Mockery;
use Ramsey\Uuid\Uuid;

class TransferApiTest extends HttpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $authorizationServiceMock = Mockery::mock(AuthorizationServiceInterface::class);
        $authorizationServiceMock->shouldReceive('authorize')
            ->andReturn(true);

        ApplicationContext::getContainer()->set(
            AuthorizationServiceInterface::class,
            $authorizationServiceMock,
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testShouldTransferSuccessfully(): void
    {
        $payer = $this->createUser('Chico', 'chico@gmail.com', UserType::COMMON);
        $payee = $this->createUser('Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);
        $this->createWallet($payer->id, 20000);
        $this->createWallet($payee->id, 5000);

        $response = $this->post('/api/v1/transfer', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100.00,
            'idempotency_key' => Uuid::uuid4()->toString(),
        ]);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('transaction_id', $response);
        $this->assertArrayHasKey('status', $response);
        $this->assertEquals('COMPLETED', $response['status']);
        $this->assertEquals(100.00, $response['amount']);
        $this->assertEquals($payer->id, $response['payer_id']);
        $this->assertEquals($payee->id, $response['payee_id']);

        $payerWallet = WalletModel::where('user_id', $payer->id)->first();
        $payeeWallet = WalletModel::where('user_id', $payee->id)->first();

        $this->assertEquals(10000, $payerWallet->balance_cents);
        $this->assertEquals(15000, $payeeWallet->balance_cents);
    }

    public function testShouldBeIdempotent(): void
    {
        $payer = $this->createUser('Chico', 'chico@gmail.com', UserType::COMMON);
        $payee = $this->createUser('Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);
        $this->createWallet($payer->id, 20000);
        $this->createWallet($payee->id, 5000);

        $idempotencyKey = Uuid::uuid4()->toString();

        $response1 = $this->post('/api/v1/transfer', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100.00,
            'idempotency_key' => $idempotencyKey,
        ]);

        $response2 = $this->post('/api/v1/transfer', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => 100.00,
            'idempotency_key' => $idempotencyKey,
        ]);

        $this->assertIsArray($response1);
        $this->assertIsArray($response2);
        $this->assertEquals('COMPLETED', $response1['status']);
        $this->assertEquals('COMPLETED', $response2['status']);
        $this->assertEquals($response1['transaction_id'], $response2['transaction_id']);

        $payerWallet = WalletModel::where('user_id', $payer->id)->first();
        $payeeWallet = WalletModel::where('user_id', $payee->id)->first();

        $this->assertEquals(10000, $payerWallet->balance_cents);
        $this->assertEquals(15000, $payeeWallet->balance_cents);
    }

    public function testShouldValidateRequest(): void
    {
        $response = $this->post('/api/v1/transfer', []);
        $this->assertIsArray($response);
        $this->assertTrue(
            isset($response['message']) || isset($response['errors']),
            'Response should contain validation errors',
        );

        $payer = $this->createUser('Chico', 'chico@gmail.com', UserType::COMMON);
        $payee = $this->createUser('Carlos Jr', 'carlosjr@gmail.com', UserType::COMMON);
        $this->createWallet($payer->id, 20000);
        $this->createWallet($payee->id, 5000);

        $response = $this->post('/api/v1/transfer', [
            'payer' => $payer->id,
            'payee' => $payee->id,
            'value' => -100.00,
            'idempotency_key' => Uuid::uuid4()->toString(),
        ]);

        $this->assertIsArray($response);
        $this->assertTrue(
            isset($response['message']) || isset($response['errors']),
            'Response should contain validation errors for negative amount',
        );
    }

    private function createUser(string $name, string $email, UserType $type): UserModel
    {
        $uniqueEmail = uniqid() . '_' . $email;

        $user = new UserModel();
        $user->name = $name;
        $user->email = $uniqueEmail;
        $user->password = password_hash('password', PASSWORD_BCRYPT);
        $user->type = $type->value;
        $user->save();

        return $user;
    }

    private function createWallet(int $userId, int $balanceCents): WalletModel
    {
        $wallet = new WalletModel();
        $wallet->user_id = $userId;
        $wallet->balance_cents = $balanceCents;
        $wallet->save();

        return $wallet;
    }
}
