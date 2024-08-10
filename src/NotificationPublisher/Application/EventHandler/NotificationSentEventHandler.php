<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Command\AddNotificationRecordCommand;
use App\NotificationPublisher\Domain\Notification\Event\NotificationSent;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status as RecordStatus;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Application\Uuid\UuidServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationSentEventHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private UuidServiceInterface $uuidService
    ) {
    }

    public function __invoke(NotificationSent $event): void
    {
        $id = $this->uuidService->generate();
        $command = new AddNotificationRecordCommand(
            $id,
            $event->aggregateId->value(),
            RecordStatus::SENT,
            $event->channel,
            ''
        );

        $this->commandBus->command($command);
    }
}
