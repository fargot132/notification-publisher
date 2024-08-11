<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;
use App\NotificationPublisher\Application\EventHandler\NotificationCreatedEventHandler;
use App\NotificationPublisher\Application\Notifier\NotifierService;
use App\NotificationPublisher\Application\Query\GetNotificationByIdQuery;
use App\NotificationPublisher\Domain\Notification\Event\NotificationCreated;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class NotificationCreatedEventHandlerTest extends TestCase
{
    private MockObject $logger;
    private MockObject $notifierService;
    private MockObject $queryBus;
    private NotificationCreatedEventHandler $handler;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->notifierService = $this->createMock(NotifierService::class);
        $this->queryBus = $this->createMock(QueryBusInterface::class);

        $this->handler = new NotificationCreatedEventHandler(
            $this->logger,
            $this->notifierService,
            $this->queryBus
        );
    }

    public function testHandleNotificationCreatedEvent(): void
    {
        $event = new NotificationCreated(new Id('123e4567-e89b-12d3-a456-426614174000'));

        $notification = new NotificationReadDto(
            '123e4567-e89b-12d3-a456-426614174000',
            '123e4567-e89b-12d3-a456-426614174000',
            'test@example.com',
            '+1234567890',
            'Test Subject',
            'Test Content',
            'new',
            '2021-01-01 00:00:00',
            '2021-01-01 00:00:00',
            0
        );

        $this->queryBus
            ->expects($this->once())
            ->method('query')
            ->with(new GetNotificationByIdQuery($event->aggregateId->value()))
            ->willReturn($notification);

        $this->notifierService
            ->expects($this->once())
            ->method('send')
            ->with($notification);

        $this->logger
            ->expects($this->never())
            ->method('error');

        ($this->handler)($event);
    }

    public function testHandleNotificationCreatedEventNotificationNotFound(): void
    {
        $event = new NotificationCreated(new Id('123e4567-e89b-12d3-a456-426614174000'));

        $this->queryBus
            ->expects($this->once())
            ->method('query')
            ->with(new GetNotificationByIdQuery($event->aggregateId->value()))
            ->willReturn(null);

        $this->notifierService
            ->expects($this->never())
            ->method('send');

        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with('Notification not found', ['notificationId' => $event->aggregateId->value()]);

        ($this->handler)($event);
    }
}
