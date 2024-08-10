<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Command\SetNotificationPendingStatusCommand;
use App\NotificationPublisher\Domain\Notification\Event\NotificationAllChannelsFailed;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationAllChannelsFailedHandler
{
    public function __construct(
        private CommandBusInterface $commandBus
    ) {
    }

    public function __invoke(NotificationAllChannelsFailed $event): void
    {
        $command = new SetNotificationPendingStatusCommand($event->aggregateId->value());

        $this->commandBus->command($command);
    }
}
