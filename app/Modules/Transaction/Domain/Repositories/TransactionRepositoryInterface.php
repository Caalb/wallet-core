<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Repositories;

use App\Modules\Transaction\Domain\Entity\Transaction;

interface TransactionRepositoryInterface
{
    public function findById(string $id): ?Transaction;

    public function findByIdempotencyKey(string $idempotencyKey): ?Transaction;

    /**
     * @return Transaction[]
     */
    public function findByUserId(int $userId): array;

    /**
     * @return Transaction[]
     */
    public function findByUserIdPaginated(int $userId, int $limit = 10, int $offset = 0): array;

    public function save(Transaction $transaction): void;

    public function delete(Transaction $transaction): void;
}
