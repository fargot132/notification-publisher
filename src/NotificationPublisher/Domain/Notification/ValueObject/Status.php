<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\ValueObject;

enum Status: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case SENT = 'sent';
    case FAILED = 'failed';
}
