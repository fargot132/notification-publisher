<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\RetryCount;
use InvalidArgumentException;

class RetryCountTest extends TestCase
{
    public function testValidRetryCount(): void
    {
        $retryCount = new RetryCount(3);
        $this->assertInstanceOf(RetryCount::class, $retryCount);
        $this->assertEquals(3, $retryCount->value());
    }

    public function testInvalidRetryCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new RetryCount(-1);
    }

    public function testEquality(): void
    {
        $retryCount1 = new RetryCount(3);
        $retryCount2 = new RetryCount(3);
        $this->assertTrue($retryCount1->equals($retryCount2));
    }

    public function testJsonSerialization(): void
    {
        $retryCount = new RetryCount(3);
        $this->assertEquals(3, $retryCount->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $retryCount = new RetryCount(3);
        $this->assertEquals('3', (string)$retryCount);
    }
}
