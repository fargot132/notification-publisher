<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use InvalidArgumentException;

class IdTest extends TestCase
{
    public function testValidUuid(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $id = new Id($uuid);
        $this->assertInstanceOf(Id::class, $id);
        $this->assertEquals($uuid, $id->value());
    }

    public function testInvalidUuid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Id('invalid-uuid');
    }

    public function testEquality(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $id1 = new Id($uuid);
        $id2 = new Id($uuid);
        $this->assertTrue($id1->equals($id2));
    }

    public function testJsonSerialization(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $id = new Id($uuid);
        $this->assertEquals($uuid, $id->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $uuid = '123e4567-e89b-12d3-a456-426614174000';
        $id = new Id($uuid);
        $this->assertEquals($uuid, (string)$id);
    }
}
