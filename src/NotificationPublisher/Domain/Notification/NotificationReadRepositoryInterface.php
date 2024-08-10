<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification;

use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;

interface NotificationReadRepositoryInterface
{
    public function findById(string $id): ?NotificationReadDto;
    public function getNotificationIdsForRetry(\DateInterval $interval): array;
}
