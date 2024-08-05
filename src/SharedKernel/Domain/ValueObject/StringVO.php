<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

abstract class StringVO implements Stringable, JsonSerializable
{
    public const MAX_LENGTH = 255;

    protected string $value;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $value)
    {
        if (!$this->validateStringLength($value)) {
            throw new InvalidArgumentException('String length exceeds ' . static::MAX_LENGTH . ' characters');
        }
        $this->value = $value;
    }

    public function equals(StringVO $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->value;
    }

    public function empty(): bool
    {
        return empty($this->value());
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validateStringLength(string $value): bool
    {
        return strlen($value) <= static::MAX_LENGTH;
    }
}
