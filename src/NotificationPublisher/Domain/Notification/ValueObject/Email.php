<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\ValueObject;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

class Email implements Stringable, JsonSerializable
{
    private string $value;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $email)
    {
        if (!$this->validateEmail($email)) {
            throw new InvalidArgumentException('Invalid email address');
        }
        $this->value = $email;
    }
    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
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
        return $this->value;
    }
}
