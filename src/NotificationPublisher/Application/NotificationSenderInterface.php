<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application;

use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;

interface NotificationSenderInterface
{
    public function send(NotificationReadDto $dto, string $channel): void;
}
