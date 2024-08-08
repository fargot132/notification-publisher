<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject;

use App\SharedKernel\Domain\ValueObject\StringVO;

class Message extends StringVO
{
    public const MAX_LENGTH = 2000;
}
