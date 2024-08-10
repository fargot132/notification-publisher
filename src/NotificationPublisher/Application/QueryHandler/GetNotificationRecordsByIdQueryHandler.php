<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\QueryHandler;

use App\NotificationPublisher\Application\Query\GetNotificationRecordsByIdQuery;
use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetNotificationRecordsByIdQueryHandler
{
    public function __construct(private NotificationReadRepositoryInterface $notificationReadRepository,)
    {
    }

    public function __invoke(GetNotificationRecordsByIdQuery $query): array
    {
        return $this->notificationReadRepository->getNotificationRecordsById($query->id);
    }
}
