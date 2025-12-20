<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\ValueObject;

use App\Modules\User\Domain\Exceptions\InvalidCPFException;
use JsonSerializable;
use Respect\Validation\Validator as v;

final readonly class CPF implements Document, JsonSerializable
{
    private string $value;

    /**
     * @throws InvalidCPFException
     */
    public function __construct(string $cpf)
    {
        $cleaned = $this->cleanCPF($cpf);

        if (!$this->isValid($cleaned)) {
            throw new InvalidCPFException($cpf);
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
            '%s.%s.%s-%s',
            substr($this->value, 0, 3),
            substr($this->value, 3, 3),
            substr($this->value, 6, 3),
            substr($this->value, 9, 2),
        );
    }

    public function equals(CPF $other): bool
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->getFormatted();
    }

    private function cleanCPF(string $cpf): string
    {
        return preg_replace('/\D/', '', $cpf);
    }

    private function isValid(string $cpf): bool
    {
        return v::cpf()->validate($cpf);
    }
}
