<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Application\DTO;

final readonly class TransferRequest
{
    public function __construct(
        public int $payerId,
        public int $payeeId,
        public float $value,
        public string $idempotencyKey,
    ) {
    }
}
