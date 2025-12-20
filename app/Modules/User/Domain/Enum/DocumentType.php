<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\Enum;

enum DocumentType: string
{
    case CPF = 'CPF';
    case CNPJ = 'CNPJ';
}
