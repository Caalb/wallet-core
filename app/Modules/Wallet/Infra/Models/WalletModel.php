<?php

declare(strict_types=1);

namespace App\Modules\Wallet\Infra\Models;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $balance_cents
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class WalletModel extends Model
{
    protected ?string $table = 'wallets';

    protected array $fillable = [
        'user_id',
        'balance_cents',
    ];

    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'balance_cents' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
