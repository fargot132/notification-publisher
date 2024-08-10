<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\QueryHandler;

use App\NotificationPublisher\Application\Query\GetThrottlingMessageCountQuery;
use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetThrottlingMessageCountQueryHandler
{
    public function __construct(private NotificationReadRepositoryInterface $notificationReadRepository,)
    {
    }

    public function __invoke(GetThrottlingMessageCountQuery $query): int
    {
        return $this->notificationReadRepository->getThrottlingMessageCount($query->userId, $query->interval);
    }
}
