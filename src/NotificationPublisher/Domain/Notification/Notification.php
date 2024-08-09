<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification;

use App\NotificationPublisher\Domain\Notification\Event\NotificationCreated;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\NotificationRecord;
use App\NotificationPublisher\Domain\Notification\ValueObject\Content;
use App\NotificationPublisher\Domain\Notification\ValueObject\Email;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use App\NotificationPublisher\Domain\Notification\ValueObject\PhoneNumber;
use App\NotificationPublisher\Domain\Notification\ValueObject\Status;
use App\NotificationPublisher\Domain\Notification\ValueObject\Subject;
use App\NotificationPublisher\Domain\Notification\ValueObject\UserId;
use App\SharedKernel\Domain\AggregateRoot;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Id as NotificationRecordId;

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

    private DateTimeImmutable $updatedAt;

    /** @var Collection<int, NotificationRecord> */
    private Collection $notificationRecords;

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
        $this->updatedAt = new DateTimeImmutable();
        $this->notificationRecords = new ArrayCollection();
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
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt
    ): self {
        $notification = new self($id, $userId, $subject, $content, $email, $phoneNumber);
        $notification->status = $status;
        $notification->createdAt = $createdAt;
        $notification->updatedAt = $updatedAt;

        return $notification;
    }

    public function addNotificationRecord(NotificationRecord $notificationRecord): void
    {
        if ($this->hasNotificationRecordWithId($notificationRecord->getId())) {
            return;
        }
        $this->notificationRecords->add($notificationRecord);
        $notificationRecord->setNotification($this);
    }

    private function hasNotificationRecordWithId(NotificationRecordId $id): bool
    {
        foreach ($this->notificationRecords as $notificationRecord) {
            if ($notificationRecord->getId()->equals($id)) {
                return true;
            }
        }

        return false;
    }
}
