<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Command\SetNotificationPendingStatusCommand;
use App\NotificationPublisher\Domain\Notification\Event\NotificationAllChannelsFailed;
use App\NotificationPublisher\Domain\Notification\ValueObject\Status;
use App\SharedKernel\Infrastructure\UseCaseBus\CommandBus;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationAllChannelsFailedHandler
{
    public function __construct(
        private CommandBus $commandBus
    ) {
    }

    public function __invoke(NotificationAllChannelsFailed $event): void
    {
        $command = new SetNotificationPendingStatusCommand($event->aggregateId->value());

        $this->commandBus->command($command);
    }
}
