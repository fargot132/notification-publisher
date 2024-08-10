<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Dto;

readonly class NotificationReadDto
{
    public function __construct(
        public string $id,
        public string $userId,
        public string $email,
        public string $phoneNumber,
        public string $subject,
        public string $content,
        public string $status,
        public string $createdAt,
        public string $updatedAt,
        public int $retryCount
    ) {
    }
}
