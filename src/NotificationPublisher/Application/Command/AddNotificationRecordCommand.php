<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Command;

use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status;

readonly class AddNotificationRecordCommand
{
    public function __construct(
        public string $id,
        public string $notificationId,
        public Status $status,
        public Channel $channel,
        public string $message
    ) {
    }
}
