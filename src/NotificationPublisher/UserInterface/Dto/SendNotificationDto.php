<?php

declare(strict_types=1);

namespace App\NotificationPublisher\UserInterface\Dto;

readonly class SendNotificationDto
{
    public function __construct(
        public string $userId,
        public string $subject,
        public string $content,
        public ?string $email,
        public ?string $phone,
    ) {
    }
}
