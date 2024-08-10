<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

readonly class RetrySendingCommand
{
    public function __construct(
        public string $id
    ) {
    }
}
