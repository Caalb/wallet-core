<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Application\DTO;

use App\Modules\Transaction\Domain\Enum\TransactionStatus;

final readonly class TransferResponse
{
    public function __construct(
        public string $transactionId,
        public int $payerId,
        public int $payeeId,
        public float $amount,
        public TransactionStatus $status,
        public string $message,
        public bool $fromCache = false,
    ) {
    }

    public function toArray(): array
    {
        return [
            'transaction_id' => $this->transactionId,
            'payer_id' => $this->payerId,
            'payee_id' => $this->payeeId,
            'amount' => $this->amount,
            'status' => $this->status->value,
            'message' => $this->message,
        ];
    }
}
