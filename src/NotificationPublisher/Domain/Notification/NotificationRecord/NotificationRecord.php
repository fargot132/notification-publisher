<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification\NotificationRecord;

use App\NotificationPublisher\Domain\Notification\Notification;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Id;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Message;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Status;
use DateTimeImmutable;

final class NotificationRecord
{
    private string $id;

    private ?Notification $notification = null;

    private Status $status;

    private Channel $channel;

    private Message $message;

    private DateTimeImmutable $createdAt;

    private function __construct(Id $id, Status $status, Channel $channel, Message $message)
    {
        $this->id = (string)$id;
        $this->status = $status;
        $this->channel = $channel;
        $this->message = $message;
        $this->createdAt = new DateTimeImmutable();
    }

    public static function create(Id $id, Status $status, Channel $channel, Message $message): self
    {
        return new self($id, $status, $channel, $message);
    }

    public static function restore(
        Id $id,
        Notification $notification,
        Status $status,
        Channel $channel,
        Message $message,
        DateTimeImmutable $createdAt
    ): self {
        $notificationRecord = new self($id, $status, $channel, $message);
        $notificationRecord->createdAt = $createdAt;
        $notificationRecord->notification = $notification;

        return $notificationRecord;
    }

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function setNotification(Notification $notification): void
    {
        $this->notification = $notification;
    }
}
