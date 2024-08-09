<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Query;

readonly class GetNotificationByIdQuery
{
    public function __construct(
        public string $id
    ) {
    }
}
