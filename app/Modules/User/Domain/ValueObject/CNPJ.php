<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\ValueObject;

use App\Modules\User\Domain\Exceptions\InvalidCNPJException;
use JsonSerializable;
use Respect\Validation\Validator as v;

final readonly class CNPJ implements Document, JsonSerializable
{
    private string $value;

    /**
     * @throws InvalidCNPJException
     */
    public function __construct(string $cnpj)
    {
        $cleaned = $this->cleanCNPJ($cnpj);

        if (!$this->isValid($cleaned)) {
            throw new InvalidCNPJException($cnpj);
        }

        $this->value = $cleaned;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getFormatted(): string
    {
        return sprintf(
            '%s.%s.%s/%s-%s',
            substr($this->value, 0, 2),
            substr($this->value, 2, 3),
            substr($this->value, 5, 3),
            substr($this->value, 8, 4),
            substr($this->value, 12, 2),
        );
    }

    public function equals(CNPJ $other): bool
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->getFormatted();
    }

    private function cleanCNPJ(string $cnpj): string
    {
        return preg_replace('/\D/', '', $cnpj);
    }

    private function isValid(string $cnpj): bool
    {
        return v::cnpj()->validate($cnpj);
    }
}
