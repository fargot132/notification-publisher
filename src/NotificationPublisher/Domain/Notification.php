<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Event\NotificationCreated;
use App\NotificationPublisher\Domain\ValueObject\Content;
use App\NotificationPublisher\Domain\ValueObject\Email;
use App\NotificationPublisher\Domain\ValueObject\Id;
use App\NotificationPublisher\Domain\ValueObject\PhoneNumber;
use App\NotificationPublisher\Domain\ValueObject\Status;
use App\NotificationPublisher\Domain\ValueObject\Subject;
use App\NotificationPublisher\Domain\ValueObject\UserId;
use App\SharedKernel\Domain\AggregateRoot;
use DateTimeImmutable;

final class Notification extends AggregateRoot
{
    private string $id;

    private UserId $userId;

    private Email $email;

    private PhoneNumber $phoneNumber;

    private Subject $subject;

    private Content $content;

    private Status $status;

    private DateTimeImmutable $createdAt;

    private function __construct(
        Id $id,
        UserId $userId,
        Subject $subject,
        Content $content,
        Email $email,
        PhoneNumber $phoneNumber
    ) {
        $this->id = (string)$id;
        $this->userId = $userId;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->subject = $subject;
        $this->content = $content;
        $this->status = Status::NEW;
        $this->createdAt = new DateTimeImmutable();
    }

    public static function create(
        Id $id,
        UserId $userId,
        Subject $subject,
        Content $content,
        Email $email,
        PhoneNumber $phoneNumber
    ): self {
        $notification = new self($id, $userId, $subject, $content, $email, $phoneNumber);
        $notification->raise(new NotificationCreated($id));

        return $notification;
    }

    public static function restore(
        Id $id,
        UserId $userId,
        Email $email,
        PhoneNumber $phoneNumber,
        Subject $subject,
        Content $content,
        Status $status,
        DateTimeImmutable $createdAt
    ): self {
        $notification = new self($id, $userId, $subject, $content, $email, $phoneNumber);
        $notification->status = $status;
        $notification->createdAt = $createdAt;

        return $notification;
    }
}
