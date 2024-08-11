<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\UserId;
use InvalidArgumentException;

class UserIdTest extends TestCase
{
    public function testValidUuid(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $userId = new UserId($uuid);
        $this->assertInstanceOf(UserId::class, $userId);
        $this->assertEquals($uuid, $userId->value());
    }

    public function testInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new UserId('invalid-uuid');
    }

    public function testEquality(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $userId1 = new UserId($uuid);
        $userId2 = new UserId($uuid);
        $this->assertTrue($userId1->equals($userId2));
    }

    public function testJsonSerialization(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $userId = new UserId($uuid);
        $this->assertEquals($uuid, $userId->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $userId = new UserId($uuid);
        $this->assertEquals($uuid, (string)$userId);
    }
}
