<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Application\Notifier;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;
use App\NotificationPublisher\Application\NotificationSenderInterface;
use App\NotificationPublisher\Application\Notifier\NotifierService;
use App\NotificationPublisher\Domain\Notification\Event\NotificationAllChannelsFailed;
use App\NotificationPublisher\Domain\Notification\Event\NotificationChannelFailed;
use App\NotificationPublisher\Domain\Notification\Event\NotificationSent;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Psr\Log\LoggerInterface;

class NotifierServiceTest extends TestCase
{
    private MockObject $notificationSender;

    private MockObject $eventBus;

    private MockObject $logger;

    private NotificationReadDto $notificationReadDto;

    protected function setUp(): void
    {
        $this->notificationSender = $this->createMock(NotificationSenderInterface::class);
        $this->eventBus = $this->createMock(EventBusInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->notificationReadDto = new NotificationReadDto(
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
    }

    public function testSendConcurrentSuccess(): void
    {
        $this->notificationSender
            ->expects($this->exactly(2))
            ->method('send')
            ->withConsecutive([$this->notificationReadDto, 'email'], [$this->notificationReadDto, 'sms']);

        $this->eventBus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->withConsecutive(
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationSent && $event->channel === Channel::EMAIL;
                    })
                ],
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationSent && $event->channel === Channel::SMS;
                    })
                ]
            );

        $notifierService = new NotifierService(
            'email,sms',
            $this->notificationSender,
            $this->eventBus,
            $this->logger
        );
        $notifierService->send($this->notificationReadDto);
    }

    public function testSendConcurrentAllChannelsFail(): void
    {
        $notifierService = new NotifierService(
            'email,sms',
            $this->notificationSender,
            $this->eventBus,
            $this->logger
        );

        $this->notificationSender
            ->expects($this->exactly(2))
            ->method('send')
            ->willThrowException(new \Exception());

        $this->eventBus
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationChannelFailed && $event->channel === Channel::EMAIL;
                    })
                ],
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationChannelFailed && $event->channel === Channel::SMS;
                    })
                ],
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationAllChannelsFailed;
                    })
                ],
            );

        $notifierService->send($this->notificationReadDto);
    }

    public function testSendFailoverSuccess(): void
    {
        $notifierService = new NotifierService(
            'failover(email,sms)',
            $this->notificationSender,
            $this->eventBus,
            $this->logger
        );

        $this->notificationSender
            ->expects($this->once())
            ->method('send')
            ->with($this->notificationReadDto, 'email');

        $this->eventBus
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->callback(function ($event) {
                    return $event instanceof NotificationSent && $event->channel === Channel::EMAIL;
                }));

        $notifierService->send($this->notificationReadDto);
    }

    public function testSendFailoverAllChannelsFail(): void
    {
        $notifierService = new NotifierService(
            'failover(email,sms)',
            $this->notificationSender,
            $this->eventBus,
            $this->logger
        );

        $this->notificationSender
            ->expects($this->exactly(2))
            ->method('send')
            ->willThrowException(new \Exception());

        $this->eventBus
            ->expects($this->exactly(3))
            ->method('dispatch')
            ->withConsecutive(
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationChannelFailed && $event->channel === Channel::EMAIL;
                    })
                ],
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationChannelFailed && $event->channel === Channel::SMS;
                    })
                ],
                [
                    $this->callback(function ($event) {
                        return $event instanceof NotificationAllChannelsFailed;
                    })
                ],
            );

        $notifierService->send($this->notificationReadDto);
    }

    public function testInvalidChannelConfiguration(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new NotifierService(
            'invalid_channel',
            $this->notificationSender,
            $this->eventBus,
            $this->logger
        );
    }
}
