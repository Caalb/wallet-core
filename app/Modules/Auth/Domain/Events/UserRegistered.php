<?php

declare(strict_types=1);

namespace App\Modules\Auth\Domain\Events;

class UserRegistered
{
    public function __construct(
        public readonly int $userId,
    ) {
    }
}
