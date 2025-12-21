<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Repositories;

use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Enum\TransactionStatus;
use App\Modules\Transaction\Domain\Repositories\TransactionRepositoryInterface;
use App\Modules\Transaction\Domain\ValueObject\TransactionId;
use App\Modules\Transaction\Infra\Models\TransactionModel;
use App\Modules\Wallet\Domain\ValueObject\Money;
use Hyperf\Redis\Redis;

use function Hyperf\Config\config;

class TransactionRepository implements TransactionRepositoryInterface
{

    public function __construct(
        private Redis $redis,
    ) {}

    public function findById(string $id): ?Transaction
    {
        $model = TransactionModel::query()->find($id);

        if (! $model) {
            return null;
        }

        return $this->mapToDomain($model);
    }

    public function findByIdempotencyKey(string $idempotencyKey): ?Transaction
    {
        $cacheKey = config('transaction.cache.prefix') . $idempotencyKey;
        $cached = $this->redis->get($cacheKey);

        if ($cached !== false) {
            $data = json_decode($cached, true);
            return $this->hydrateDomain($data);
        }

        $model = TransactionModel::query()
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        if (! $model) {
            return null;
        }

        $this->cacheTransaction($model);

        return $this->mapToDomain($model);
    }

    public function findByUserId(int $userId): array
    {
        $models = TransactionModel::query()
            ->where('payer_id', $userId)
            ->orWhere('payee_id', $userId)
            ->orderByDesc('created_at')
            ->get();

        return $models->map(fn($model) => $this->mapToDomain($model))->all();
    }

    public function findByUserIdPaginated(int $userId, int $limit = 10, int $offset = 0): array
    {
        $models = TransactionModel::query()
            ->where('payer_id', $userId)
            ->orWhere('payee_id', $userId)
            ->orderByDesc('created_at')
            ->limit($limit)
            ->offset($offset)
            ->get();

        return $models->map(fn($model) => $this->mapToDomain($model))->all();
    }

    public function save(Transaction $transaction): void
    {
        $model = TransactionModel::query()->findOrNew($transaction->getId());

        $model->id = $transaction->getId();
        $model->payer_id = $transaction->getPayerId();
        $model->payee_id = $transaction->getPayeeId();
        $model->amount_cents = $transaction->getAmount()->getAmountInCents();
        $model->status = $transaction->getStatus()->value;
        $model->failure_reason = $transaction->getFailureReason();
        $model->idempotency_key = $transaction->getIdempotencyKey();
        $model->completed_at = $transaction->getCompletedAt();
        $model->failed_at = $transaction->getFailedAt();

        $model->save();

        $model->refresh();

        $this->cacheTransaction($model);
    }

    public function delete(Transaction $transaction): void
    {
        TransactionModel::query()
            ->where('id', $transaction->getId())
            ->delete();

        $cacheKey = config('transaction.cache.prefix') . $transaction->getIdempotencyKey();
        $this->redis->del($cacheKey);
    }

    private function cacheTransaction(TransactionModel $model): void
    {
        if (!$model->idempotency_key) {
            return;
        }

        $cacheKey = config('transaction.cache.prefix') . $model->idempotency_key;

        $data = [
            'id' => $model->id,
            'payer_id' => $model->payer_id,
            'payee_id' => $model->payee_id,
            'amount_cents' => $model->amount_cents,
            'status' => $model->status,
            'failure_reason' => $model->failure_reason,
            'idempotency_key' => $model->idempotency_key,
            'completed_at' => $model->completed_at?->format('Y-m-d H:i:s'),
            'failed_at' => $model->failed_at?->format('Y-m-d H:i:s'),
            'created_at' => $model->created_at?->format('Y-m-d H:i:s') ?? date('Y-m-d H:i:s'),
        ];

        $this->redis->setex($cacheKey, config('transaction.cache.ttl'), json_encode($data));
    }

    private function mapToDomain(TransactionModel $model): Transaction
    {
        return new Transaction(
            id: TransactionId::fromString($model->id),
            payerId: $model->payer_id,
            payeeId: $model->payee_id,
            amount: Money::fromCents($model->amount_cents),
            status: TransactionStatus::from($model->status),
            failureReason: $model->failure_reason,
            idempotencyKey: $model->idempotency_key,
            completedAt: $model->completed_at?->toDateTimeImmutable(),
            failedAt: $model->failed_at?->toDateTimeImmutable(),
            createdAt: $model->created_at->toDateTimeImmutable(),
        );
    }

    private function hydrateDomain(array $data): Transaction
    {
        return new Transaction(
            id: TransactionId::fromString($data['id']),
            payerId: $data['payer_id'],
            payeeId: $data['payee_id'],
            amount: Money::fromCents($data['amount_cents']),
            status: TransactionStatus::from($data['status']),
            failureReason: $data['failure_reason'],
            idempotencyKey: $data['idempotency_key'],
            completedAt: isset($data['completed_at']) ? new \DateTimeImmutable($data['completed_at']) : null,
            failedAt: isset($data['failed_at']) ? new \DateTimeImmutable($data['failed_at']) : null,
            createdAt: new \DateTimeImmutable($data['created_at']),
        );
    }
}
