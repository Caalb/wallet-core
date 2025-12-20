<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Enum;

enum UserType: string
{
    case COMMON = 'COMMON';
    case MERCHANT = 'MERCHANT';

    public function isCommon(): bool
    {
        return $this === self::COMMON;
    }

    public function isMerchant(): bool
    {
        return $this === self::MERCHANT;
    }
}
