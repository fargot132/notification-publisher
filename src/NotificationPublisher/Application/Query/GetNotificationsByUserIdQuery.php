<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Query;

readonly class GetNotificationsByUserIdQuery
{
    public function __construct(
        public string $userId,
        public int $limit,
        public int $offset
    ) {
    }
}
