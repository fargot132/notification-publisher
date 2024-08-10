<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\QueryHandler;

use App\NotificationPublisher\Application\Query\GetNotificationIdsForRetryQuery;
use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetNotificationIdsForRetryQueryHandler
{
    public function __construct(private NotificationReadRepositoryInterface $notificationReadRepository,)
    {
    }

    /**
     * @return array<string>
     */
    public function __invoke(GetNotificationIdsForRetryQuery $query): array
    {
        return $this->notificationReadRepository->getNotificationIdsForRetry($query->interval);
    }
}
