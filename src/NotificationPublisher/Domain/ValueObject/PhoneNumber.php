<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

class PhoneNumber implements Stringable, JsonSerializable
{
    private string $phoneNumber;

    public function __construct(string $phoneNumber)
    {
        $phoneNumber = $this->sanitizePhoneNumber($phoneNumber);
        if (!$this->validatePhoneNumber($phoneNumber)) {
            throw new InvalidArgumentException('Invalid phone number');
        }
        $this->phoneNumber = $phoneNumber;
    }
    public function __toString()
    {
        return $this->phoneNumber;
    }

    public function jsonSerialize(): mixed
    {
        return $this->phoneNumber;
    }

    public function equals(PhoneNumber $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->phoneNumber;
    }

    private function validatePhoneNumber(string $phoneNumber): bool
    {
        return preg_match('/^\+?[0-9]{1,4}[0-9]{6,14}$/', $phoneNumber) === 1;
    }

    private function sanitizePhoneNumber(string $phoneNumber): string
    {
        return preg_replace('/[^0-9+]/', '', $phoneNumber);
    }
}
