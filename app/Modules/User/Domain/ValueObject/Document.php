<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\ValueObject;

interface Document
{
    public function __toString(): string;

    public function getValue(): string;
}
