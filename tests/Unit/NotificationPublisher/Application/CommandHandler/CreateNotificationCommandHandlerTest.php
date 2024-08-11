<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\CreateNotificationCommand;
use App\NotificationPublisher\Application\CommandHandler\CreateNotificationCommandHandler;
use App\NotificationPublisher\Domain\Notification\Event\NotificationCreated;
use App\NotificationPublisher\Domain\Notification\Notification;
use App\NotificationPublisher\Domain\Notification\NotificationFactory;
use App\NotificationPublisher\Domain\Notification\NotificationRepositoryInterface;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class CreateNotificationCommandHandlerTest extends TestCase
{
    private MockObject $notificationFactory;
    private MockObject $notificationRepository;
    private MockObject $eventBus;
    private CreateNotificationCommandHandler $handler;

    protected function setUp(): void
    {
        $this->notificationFactory = $this->createMock(NotificationFactory::class);
        $this->notificationRepository = $this->createMock(NotificationRepositoryInterface::class);
        $this->eventBus = $this->createMock(EventBusInterface::class);

        $this->handler = new CreateNotificationCommandHandler(
            $this->notificationFactory,
            $this->notificationRepository,
            $this->eventBus
        );
    }

    public function testHandleCreateNotificationCommand(): void
    {
        $command = new CreateNotificationCommand(
            '123e4567-e89b-12d3-a456-426614174000',
            '123e4567-e89b-12d3-a456-426614174000',
            'Test Subject',
            'Test Content',
            'test@example.com',
            '+1234567890'
        );

        $notification = $this->createMock(Notification::class);
        $notification->method('pullEvents')->willReturn([new NotificationCreated(new Id($command->id))]);

        $this->notificationFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $command->id,
                $command->userId,
                $command->subject,
                $command->content,
                $command->email,
                $command->phone
            )
            ->willReturn($notification);

        $this->notificationRepository
            ->expects($this->once())
            ->method('save')
            ->with($notification);

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch');

        ($this->handler)($command);
    }
}
