<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\Content;
use InvalidArgumentException;

class ContentTest extends TestCase
{
    public function testValidContent(): void
    {
        $content = new Content('Valid content');
        $this->assertInstanceOf(Content::class, $content);
        $this->assertEquals('Valid content', $content->value());
    }

    public function testInvalidContent(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Content(str_repeat('a', Content::MAX_LENGTH + 1));
    }

    public function testEquality(): void
    {
        $content1 = new Content('Same content');
        $content2 = new Content('Same content');
        $this->assertTrue($content1->equals($content2));
    }

    public function testEmptyContent(): void
    {
        $content = new Content('');
        $this->assertTrue($content->empty());
    }

    public function testJsonSerialization(): void
    {
        $content = new Content('JSON content');
        $this->assertEquals('JSON content', $content->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $content = new Content('String content');
        $this->assertEquals('String content', (string)$content);
    }
}
