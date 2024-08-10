<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use Stringable;

class IntegerVO implements Stringable, \JsonSerializable
{
    protected int $value;

    public function __construct(int $value)
    {
        $this->value = $value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }

    public function equals(IntegerVO $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): int
    {
        return $this->value;
    }

    public function jsonSerialize(): int
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string)$this->value;
    }
}
