<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\QueryHandler;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;
use App\NotificationPublisher\Application\NotificationReadRepositoryInterface;
use App\NotificationPublisher\Application\Query\GetNotificationsByUserIdQuery;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class GetNotificationsByUserIdQueryHandler
{
    public function __construct(private NotificationReadRepositoryInterface $notificationReadRepository,)
    {
    }

    /**
     * @return array<NotificationReadDto>
     */
    public function __invoke(GetNotificationsByUserIdQuery $query): array
    {
        return $this->notificationReadRepository
            ->getNotificationsByUserId($query->userId, $query->limit, $query->offset);
    }
}
