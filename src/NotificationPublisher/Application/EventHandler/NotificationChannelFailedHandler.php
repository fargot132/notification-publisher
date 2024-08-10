<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Command\AddNotificationRecordCommand;
use App\NotificationPublisher\Domain\Notification\Event\NotificationChannelFailed;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status as RecordStatus;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Application\Uuid\UuidServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationChannelFailedHandler
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private UuidServiceInterface $uuidService
    ) {
    }

    public function __invoke(NotificationChannelFailed $event): void
    {
        $id = $this->uuidService->generate();
        $command = new AddNotificationRecordCommand(
            $id,
            $event->aggregateId->value(),
            RecordStatus::FAILED,
            $event->channel,
            ''
        );

        $this->commandBus->command($command);
    }
}
