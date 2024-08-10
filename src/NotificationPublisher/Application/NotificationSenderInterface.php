<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;

interface NotificationSenderInterface
{
    public function send(NotificationReadDto $dto, string $channel): void;
}
