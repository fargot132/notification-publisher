<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\AddNotificationRecordCommand;
use App\NotificationPublisher\Application\CommandHandler\AddNotificationRecordCommandHandler;
use App\NotificationPublisher\Domain\Notification\Notification;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status;
use App\NotificationPublisher\Domain\Notification\NotificationRepositoryInterface;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class AddNotificationRecordCommandHandlerTest extends TestCase
{
    private MockObject $notificationRepository;
    private MockObject $eventBus;
    private AddNotificationRecordCommandHandler $handler;

    protected function setUp(): void
    {
        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->eventBus = $this->createMock(EventBusInterface::class);

        $this->handler = new AddNotificationRecordCommandHandler(
            $this->notificationRepository,
            $this->eventBus
        );
    }

    public function testHandleAddNotificationRecordCommand(): void
    {
        $command = new AddNotificationRecordCommand(
            '123e4567-e89b-12d3-a456-426614174000',
            '123e4567-e89b-12d3-a456-426614174001',
            Status::SENT,
            Channel::EMAIL,
            'Message content'
        );

        $notification = $this->createMock(Notification::class);
        $notification->method('pullEvents')->willReturn([]);

        $this->notificationRepository
            ->expects($this->once())
            ->method('get')
            ->with($command->notificationId)
            ->willReturn($notification);

        $this->notificationRepository
            ->expects($this->once())
            ->method('save')
            ->with($notification);

        $this->eventBus
            ->expects($this->exactly(0))
            ->method('dispatch');

        ($this->handler)($command);
    }
}
