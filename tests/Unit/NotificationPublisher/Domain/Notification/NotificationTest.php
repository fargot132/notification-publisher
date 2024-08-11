<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Domain\Notification;

use App\NotificationPublisher\Domain\Notification\Event\NotificationCreated;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Message;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status as NotificationRecordStatus;
use App\NotificationPublisher\Domain\Notification\ValueObject\RetryCount;
use App\NotificationPublisher\Domain\Notification\ValueObject\Status;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Domain\Notification\Notification;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use App\NotificationPublisher\Domain\Notification\ValueObject\UserId;
use App\NotificationPublisher\Domain\Notification\ValueObject\Subject;
use App\NotificationPublisher\Domain\Notification\ValueObject\Content;
use App\NotificationPublisher\Domain\Notification\ValueObject\Email;
use App\NotificationPublisher\Domain\Notification\ValueObject\PhoneNumber;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\NotificationRecord;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Id as NotificationRecordId;
use DateTimeImmutable;
use ReflectionClass;

class NotificationTest extends TestCase
{
    public function testNotificationCreate(): void
    {
        $properties = $this->getNotificationProperties();

        $notification = Notification::create(
            $properties['id'],
            $properties['userId'],
            $properties['subject'],
            $properties['content'],
            $properties['email'],
            $properties['phoneNumber']
        );

        $this->assertInstanceOf(Notification::class, $notification);

        $reflection = new ReflectionClass($notification);

        foreach ($properties as $property => $value) {
            $property = $reflection->getProperty($property);
            $this->assertEquals($value, $property->getValue($notification));
        }

        $createdAtProperty = $reflection->getProperty('createdAt');
        $createdAt = $createdAtProperty->getValue($notification);

        $updatedAtProperty = $reflection->getProperty('updatedAt');
        $updatedAt = $updatedAtProperty->getValue($notification);

        $this->assertInstanceOf(DateTimeImmutable::class, $createdAt);
        $this->assertInstanceOf(DateTimeImmutable::class, $updatedAt);
        $this->assertEquals($createdAt, $updatedAt);

        $events = $notification->pullEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(NotificationCreated::class, $events[0]);

        $events = $notification->pullEvents();
        $this->assertCount(0, $events);
    }

    public function testAddNotificationRecord(): void
    {
        $properties = $this->getNotificationProperties();

        $notification = Notification::create(
            $properties['id'],
            $properties['userId'],
            $properties['subject'],
            $properties['content'],
            $properties['email'],
            $properties['phoneNumber']
        );

        $notificationRecord = NotificationRecord::create(
            new NotificationRecordId('123e4567-e89b-12d3-a456-426614174001'),
            NotificationRecordStatus::FAILED,
            Channel::EMAIL,
            new Message('Test message 1')
        );

        $notification->addNotificationRecord($notificationRecord);

        $reflection = new ReflectionClass($notification);
        $notificationRecordsProperty = $reflection->getProperty('notificationRecords');
        $notificationRecords = $notificationRecordsProperty->getValue($notification);
        $statusProperty = $reflection->getProperty('status');
        $status = $statusProperty->getValue($notification);

        $this->assertCount(1, $notificationRecords);
        $this->assertEquals(Status::NEW, $status);

        $notificationRecord = NotificationRecord::create(
            new NotificationRecordId('123e4567-e89b-12d3-a456-426614174002'),
            NotificationRecordStatus::SENT,
            Channel::EMAIL,
            new Message('Test message 2')
        );

        $notification->addNotificationRecord($notificationRecord);

        $notificationRecords = $notificationRecordsProperty->getValue($notification);
        $status = $statusProperty->getValue($notification);
        $this->assertCount(2, $notificationRecords);
        $this->assertEquals(Status::SENT, $status);
    }

    public function testSetPendingStatus(): void
    {
        $properties = $this->getNotificationProperties();

        $notification = Notification::create(
            $properties['id'],
            $properties['userId'],
            $properties['subject'],
            $properties['content'],
            $properties['email'],
            $properties['phoneNumber']
        );

        $notification->setPendingStatus();

        $reflection = new ReflectionClass($notification);
        $statusProperty = $reflection->getProperty('status');
        $status = $statusProperty->getValue($notification);

        $this->assertEquals(Status::PENDING, $status);

        $notification->retrySending();
        $notification->retrySending();
        $notification->retrySending();
        $notification->setPendingStatus();

        $status = $statusProperty->getValue($notification);
        $this->assertEquals(Status::FAILED, $status);
    }

    public function testRetrySending(): void
    {
        $properties = $this->getNotificationProperties();

        $notification = Notification::create(
            $properties['id'],
            $properties['userId'],
            $properties['subject'],
            $properties['content'],
            $properties['email'],
            $properties['phoneNumber']
        );

        $notification->setPendingStatus();
        $notification->retrySending();

        $reflection = new ReflectionClass($notification);
        $retryCountProperty = $reflection->getProperty('retryCount');
        $retryCount = $retryCountProperty->getValue($notification);
        $statusProperty = $reflection->getProperty('status');
        $status = $statusProperty->getValue($notification);

        $this->assertEquals(1, $retryCount->value());
        $this->assertEquals(Status::PENDING, $status);

        $notification->retrySending();
        $notification->retrySending();
        $notification->retrySending();

        $status = $statusProperty->getValue($notification);
        $retryCount = $retryCountProperty->getValue($notification);
        $this->assertEquals(3, $retryCount->value());
        $this->assertEquals(Status::FAILED, $status);
    }

    private function getNotificationProperties(): array
    {
        return [
            'id' => new Id('123e4567-e89b-12d3-a456-426614174000'),
            'userId' => new UserId('123e4567-e89b-12d3-a456-426614174000'),
            'subject' => new Subject('Test Subject'),
            'content' => new Content('Test Content'),
            'email' => new Email('test@example.com'),
            'phoneNumber' => new PhoneNumber('+1234567890'),
            'status' => Status::NEW,
            'retryCount' => new RetryCount(0),
            'notificationRecords' => new ArrayCollection(),
        ];
    }
}
