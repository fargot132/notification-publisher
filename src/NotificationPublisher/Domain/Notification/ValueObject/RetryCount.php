<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\ValueObject;

use App\SharedKernel\Domain\ValueObject\PositiveIntegerVO;

class RetryCount extends PositiveIntegerVO
{
}
