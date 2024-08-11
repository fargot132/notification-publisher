<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification\ValueObject;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\ValueObject\Subject;
use InvalidArgumentException;

class SubjectTest extends TestCase
{
    public function testValidSubject(): void
    {
        $subject = new Subject('Valid subject');
        $this->assertInstanceOf(Subject::class, $subject);
        $this->assertEquals('Valid subject', $subject->value());
    }

    public function testInvalidSubject(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Subject(str_repeat('a', Subject::MAX_LENGTH + 1));
    }

    public function testEquality(): void
    {
        $subject1 = new Subject('Same subject');
        $subject2 = new Subject('Same subject');
        $this->assertTrue($subject1->equals($subject2));
    }

    public function testJsonSerialization(): void
    {
        $subject = new Subject('JSON subject');
        $this->assertEquals('JSON subject', $subject->jsonSerialize());
    }

    public function testStringRepresentation(): void
    {
        $subject = new Subject('String subject');
        $this->assertEquals('String subject', (string)$subject);
    }
}
