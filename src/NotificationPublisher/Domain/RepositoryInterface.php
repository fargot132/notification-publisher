<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

interface RepositoryInterface
{
    public function save(Notification $notification): void;
    public function get(int $id): ?Notification;
}
