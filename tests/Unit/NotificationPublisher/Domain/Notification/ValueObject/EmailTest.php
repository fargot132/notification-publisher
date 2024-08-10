<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\Email;
use InvalidArgumentException;

class EmailTest extends TestCase
{
    public function testValidEmail(): void
    {
        $email = new Email('test@example.com');
        $this->assertInstanceOf(Email::class, $email);
        $this->assertEquals('test@example.com', $email->value());
    }

    public function testInvalidEmail(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Email('invalid-email');
    }

    public function testEquality(): void
    {
        $email1 = new Email('test@example.com');
        $email2 = new Email('test@example.com');
        $this->assertTrue($email1->equals($email2));
    }

    public function testJsonSerialization(): void
    {
        $email = new Email('json@example.com');
        $this->assertEquals('json@example.com', $email->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $email = new Email('string@example.com');
        $this->assertEquals('string@example.com', (string)$email);
    }
}
