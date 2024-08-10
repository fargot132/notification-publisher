<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Dto;

readonly class NotificationRecordReadDto
{
    public function __construct(
        public string $id,
        public string $notificationId,
        public string $status,
        public string $channel,
        public string $message,
        public string $createdAt
    ) {
    }
}
