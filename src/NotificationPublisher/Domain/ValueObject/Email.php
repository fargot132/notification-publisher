<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

class Email implements Stringable, JsonSerializable
{
    private string $email;

    public function __construct(string $email)
    {
        if (!$this->validateEmail($email)) {
            throw new InvalidArgumentException('Invalid email address');
        }
        $this->email = $email;
    }
    public function __toString(): string
    {
        return $this->email;
    }

    public function jsonSerialize(): string
    {
        return $this->email;
    }

    private function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function equals(Email $other): bool
    {
        return $this->value() === $other->value();
    }

    public function value(): string
    {
        return $this->email;
    }
}
