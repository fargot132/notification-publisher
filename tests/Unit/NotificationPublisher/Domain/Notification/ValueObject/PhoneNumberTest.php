<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\PhoneNumber;
use InvalidArgumentException;

class PhoneNumberTest extends TestCase
{
    public function testValidPhoneNumber(): void
    {
        $phoneNumber = new PhoneNumber('+1234567890');
        $this->assertInstanceOf(PhoneNumber::class, $phoneNumber);
        $this->assertEquals('+1234567890', $phoneNumber->value());
    }

    public function testInvalidPhoneNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new PhoneNumber('invalid-phone-number');
    }

    public function testEquality(): void
    {
        $phoneNumber1 = new PhoneNumber('+1234567890');
        $phoneNumber2 = new PhoneNumber('+1234567890');
        $this->assertTrue($phoneNumber1->equals($phoneNumber2));
    }

    public function testJsonSerialization(): void
    {
        $phoneNumber = new PhoneNumber('+1234567890');
        $this->assertEquals('+1234567890', $phoneNumber->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $phoneNumber = new PhoneNumber('+1234567890');
        $this->assertEquals('+1234567890', (string)$phoneNumber);
    }
}
