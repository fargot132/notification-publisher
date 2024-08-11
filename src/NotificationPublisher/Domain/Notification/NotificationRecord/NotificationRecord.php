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

    /** @phpstan-ignore-next-line */
    private ?Notification $notification = null;

    private Status $status;

    /** @phpstan-ignore-next-line */
    private Channel $channel;

    /** @phpstan-ignore-next-line */
    private Message $message;

    /** @phpstan-ignore-next-line */
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

    public function getId(): Id
    {
        return new Id($this->id);
    }

    public function setNotification(Notification $notification): void
    {
        $this->notification = $notification;
    }

    public function getStatus(): Status
    {
        return $this->status;
    }
}
