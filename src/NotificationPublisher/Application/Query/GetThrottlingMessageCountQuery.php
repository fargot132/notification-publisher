<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Query;

readonly class GetThrottlingMessageCountQuery
{
    public function __construct(
        public string $userId,
        public string $interval
    ) {
    }
}
