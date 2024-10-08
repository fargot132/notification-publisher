<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

class PhoneNumber implements Stringable, JsonSerializable
{
    private string $value;

    public function __construct(string $phoneNumber)
    {
        $phoneNumber = $this->sanitizePhoneNumber($phoneNumber);
        if (!$this->validatePhoneNumber($phoneNumber)) {
            throw new InvalidArgumentException('Invalid phone number');
        }
        $this->value = $phoneNumber;
    }
    public function __toString()
    {
        return $this->value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }

    public function equals(PhoneNumber $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->value;
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
