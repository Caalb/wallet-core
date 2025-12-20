<?php

declare(strict_types=1);

namespace App\Modules\User\Domain\ValueObject;

use App\Modules\User\Domain\Exceptions\InvalidEmailException;
use JsonSerializable;
use Respect\Validation\Validator as v;

final readonly class Email implements JsonSerializable
{
    private string $value;

    /**
     * @throws InvalidEmailException
     */
    public function __construct(string $email)
    {
        $normalized = strtolower(trim($email));

        if (!v::email()->validate($normalized)) {
            throw new InvalidEmailException($email);
        }

        $this->value = $normalized;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDomain(): string
    {
        return substr($this->value, strpos($this->value, '@') + 1);
    }

    public function getLocalPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }
}
