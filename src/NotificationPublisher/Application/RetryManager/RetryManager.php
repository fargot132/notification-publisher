<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\RetryManager;

use App\NotificationPublisher\Application\Command\RetrySendingCommand;
use App\NotificationPublisher\Application\Query\GetNotificationIdsForRetryQuery;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;

class RetryManager
{
    public function __construct(
        private string $retryInterval,
        private QueryBusInterface $queryBus,
        private CommandBusInterface $commandBus
    ) {
    }

    public function retrySending(): void
    {
        $notificationIds = $this->queryBus->query(new GetNotificationIdsForRetryQuery($this->retryInterval));

        foreach ($notificationIds as $notificationId) {
            $this->commandBus->command(new RetrySendingCommand($notificationId));
        }
    }
}
