<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;

interface NotificationReadRepositoryInterface
{
    public function findById(string $id): ?NotificationReadDto;
    public function getNotificationIdsForRetry(string $interval): array;
    public function getThrottlingMessageCount(string $userId, string $interval): int;
    public function getNotificationRecordsById(string $id): array;
    public function getNotificationsByUserId(string $userId, int $limit, int $offset): array;
}
