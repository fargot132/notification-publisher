<?php

namespace App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject;

enum Channel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}
