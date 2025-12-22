<?php

declare(strict_types=1);

namespace App\Modules\Transaction\Domain\Services;

use App\Modules\Transaction\Domain\Entity\Transaction;
use App\Modules\Transaction\Domain\Exceptions\AuthorizationServiceException;

interface AuthorizationServiceInterface
{
    /**
     * @throws AuthorizationServiceException
     */
    public function authorize(Transaction $transaction): bool;
}
