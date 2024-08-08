<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification;

interface NotificationRepositoryInterface
{
    public function save(Notification $notification): void;
    public function get(string $id): ?Notification;
}
