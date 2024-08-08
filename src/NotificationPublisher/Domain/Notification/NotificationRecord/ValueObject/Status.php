<?php

namespace App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject;

enum Status: string
{
    case SENT = 'sent';
    case FAILED = 'failed';
    case RETRY = 'retry';
}
