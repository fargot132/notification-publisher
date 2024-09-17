<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;
use App\NotificationPublisher\Application\Dto\NotificationRecordReadDto;

interface NotificationReadRepositoryInterface
{
    public function findById(string $id): ?NotificationReadDto;

    /**
     * @return array<string>
     */
    public function getNotificationIdsForRetry(string $interval): array;

    public function getThrottlingMessageCount(string $userId, string $interval): int;

    /**
     * @return array<NotificationRecordReadDto>
     */
    public function getNotificationRecordsById(string $id): array;

    /**
     * @return array<NotificationReadDto>
     */
    public function getNotificationsByUserId(string $userId, int $limit, int $offset): array;
}
