<?php

namespace App\NotificationPublisher\Application\Enum;

enum Channel: string
{
    case EMAIL = 'email';
    case SMS = 'sms';
}
