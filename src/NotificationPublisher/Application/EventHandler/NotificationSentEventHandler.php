<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Command\AddNotificationRecordCommand;
use App\NotificationPublisher\Application\Command\SetNotificationPendingStatusCommand;
use App\NotificationPublisher\Domain\Notification\Event\NotificationSent;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status as RecordStatus;
use App\SharedKernel\Infrastructure\UseCaseBus\CommandBus;
use App\SharedKernel\Infrastructure\Uuid\UuidServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\NotificationPublisher\Domain\Notification\ValueObject\Status;

#[AsMessageHandler]
class NotificationSentEventHandler
{
    public function __construct(
        private CommandBus $commandBus,
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
