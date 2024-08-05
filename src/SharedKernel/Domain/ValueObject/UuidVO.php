<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

abstract class UuidVO implements Stringable, JsonSerializable
{
    private string $uuid;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $uuid)
    {
        if (!$this->validateUuid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID format');
        }
        $this->uuid = $uuid;
    }

    private function validateUuid(string $uuid): bool
    {
        return preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $uuid) === 1;
    }

    public function equals(UuidVO $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->uuid;
    }

    public function __toString()
    {
        return $this->uuid;
    }

    public function jsonSerialize(): string
    {
        return $this->uuid;
    }
}
