<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\Event;

use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\SharedKernel\Domain\DomainEvent;
use App\SharedKernel\Domain\ValueObject\UuidVO;

class NotificationChannelFailed extends DomainEvent
{

    public function __construct(UuidVO $aggregateId, readonly public Channel $channel)
    {
        parent::__construct($aggregateId);
    }
}
