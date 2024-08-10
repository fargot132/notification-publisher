<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\RetryManager;

use App\NotificationPublisher\Application\Command\RetrySendingCommand;
use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
use App\SharedKernel\Infrastructure\UseCaseBus\CommandBus;
use DateInterval;

class RetryManager
{
    public function __construct(
        private string $retryInterval,
        private NotificationReadRepositoryInterface $notificationReadRepository,
        private CommandBus $commandBus
    ) {
    }

    public function retrySending(): void
    {
        $notificationIds = $this->notificationReadRepository->getNotificationIdsForRetry(
            DateInterval::createFromDateString($this->retryInterval)
        );

        foreach ($notificationIds as $notificationId) {
            $this->commandBus->command(new RetrySendingCommand($notificationId));
        }
    }
}
