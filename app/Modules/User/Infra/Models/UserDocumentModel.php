<?php

declare(strict_types=1);

namespace App\Modules\User\Infra\Models;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $document
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserDocumentModel extends Model
{
    protected ?string $table = 'user_documents';

    protected array $fillable = [
        'user_id',
        'document',
        'type',
    ];

    protected array $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
