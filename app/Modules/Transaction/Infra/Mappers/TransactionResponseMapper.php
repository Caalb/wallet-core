<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Mappers;

use App\Modules\Transaction\Application\DTO\TransferResponse;
use App\Modules\Transaction\Domain\Enum\TransactionStatus;

class TransactionResponseMapper
{
    public static function toHttpResponse(TransferResponse $response): array
    {
        return [
            'data' => $response->toArray(),
            'status_code' => self::mapStatusCode($response->status),
        ];
    }

    private static function mapStatusCode(TransactionStatus $status): int
    {
        return match ($status) {
            TransactionStatus::COMPLETED => 200,
            TransactionStatus::FAILED => 422,
            default => 500,
        };
    }
}
