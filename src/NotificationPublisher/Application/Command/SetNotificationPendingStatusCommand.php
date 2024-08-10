<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

readonly class SetNotificationPendingStatusCommand
{
    public function __construct(
        public string $id
    ) {
    }
}
