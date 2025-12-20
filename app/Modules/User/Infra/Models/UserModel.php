<?php

declare(strict_types=1);

namespace App\Modules\User\Infra\Models;

use Carbon\Carbon;
use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $document
 * @property string $email
 * @property string $password
 * @property string $type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class UserModel extends Model
{
    protected ?string $table = 'users';

    protected array $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

    protected array $hidden = [
        'password',
    ];

    protected array $casts = [
        'id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
