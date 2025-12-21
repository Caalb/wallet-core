<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Infra\Models;

use Carbon\Carbon;
use Hyperf\Database\Model\Concerns\CamelCase;
use Hyperf\DbConnection\Model\Model;

/**
 * @property string $id
 * @property int $payer_id
 * @property int $payee_id
 * @property int $amount_cents
 * @property string $status
 * @property null|string $failure_reason
 * @property string $idempotency_key
 * @property null|Carbon $completed_at
 * @property null|Carbon $failed_at
 * @property Carbon $created_at
 */
class TransactionModel extends Model
{
    use CamelCase;


    public bool $incrementing = false;

    public bool $timestamps = false;

    protected ?string $table = 'transactions';

    protected string $primaryKey = 'id';

    protected string $keyType = 'string';

    protected array $fillable = [
        'id',
        'payer_id',
        'payee_id',
        'amount_cents',
        'status',
        'failure_reason',
        'idempotency_key',
        'completed_at',
        'failed_at',
        'created_at',
    ];

    protected array $casts = [
        'amount_cents' => 'integer',
        'completed_at' => 'datetime',
        'failed_at' => 'datetime',
        'created_at' => 'datetime',
    ];
}
