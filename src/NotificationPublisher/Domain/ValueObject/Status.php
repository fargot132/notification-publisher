<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\ValueObject;

enum Status: string
{
    case NEW = 'new';
    case PENDING = 'pending';
    case SENT = 'sent';
    case PARTIALLY_SENT = 'partially_sent';
    case FAILED = 'failed';
}
