<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\ValueObject;

use App\SharedKernel\Domain\ValueObject\StringVO;

class Content extends StringVO
{
    public const MAX_LENGTH = 2000;
}
