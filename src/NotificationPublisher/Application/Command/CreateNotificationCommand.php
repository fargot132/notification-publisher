<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

readonly class CreateNotificationCommand
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $subject,
        public string $content,
        public string $email,
        public string $phone,
    ) {
    }
}
