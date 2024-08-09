<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\QueryHandler;

use App\NotificationPublisher\Application\Query\GetNotificationByIdQuery;
use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetNotificationByIdQueryHandler
{
    public function __construct( private NotificationReadRepositoryInterface $notificationReadRepository)
    {
    }
    public function __invoke(GetNotificationByIdQuery $query): ?NotificationReadDto
    {
        return $this->notificationReadRepository->findById($query->id);
    }
}
