<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Event\NotificationCreated;
use App\NotificationPublisher\Domain\Exception\NullEmailAndPhoneNumberException;
use App\NotificationPublisher\Domain\ValueObject\Content;
use App\NotificationPublisher\Domain\ValueObject\Email;
use App\NotificationPublisher\Domain\ValueObject\Id;
use App\NotificationPublisher\Domain\ValueObject\PhoneNumber;
use App\NotificationPublisher\Domain\ValueObject\Status;
use App\NotificationPublisher\Domain\ValueObject\Subject;
use App\NotificationPublisher\Domain\ValueObject\UserId;
use App\SharedKernel\Domain\AggregateRoot;

final class Notification extends AggregateRoot
{
    private string $id;

    private UserId $userId;

    private ?Email $email;

    private ?PhoneNumber $phoneNumber;

    private Subject $subject;

    private Content $content;

    private Status $status;

    /**
     * @throws NullEmailAndPhoneNumberException
     */
    private function __construct(
        Id $id,
        UserId $userId,
        Subject $subject,
        Content $content,
        ?Email $email,
        ?PhoneNumber $phoneNumber
    ) {
        if ($email === null && $phoneNumber === null) {
            throw new NullEmailAndPhoneNumberException('Email or phone number must be provided');
        }
        $this->id = (string)$id;
        $this->userId = $userId;
        $this->email = $email;
        $this->phoneNumber = $phoneNumber;
        $this->subject = $subject;
        $this->content = $content;
        $this->status = Status::NEW;
    }

    /**
     * @throws NullEmailAndPhoneNumberException
     */
    public static function create(
        Id $id,
        UserId $userId,
        Subject $subject,
        Content $content,
        ?Email $email,
        ?PhoneNumber $phoneNumber
    ): self {
        $notification = new self($id, $userId, $subject, $content, $email, $phoneNumber);
        $notification->raise(new NotificationCreated($id));

        return $notification;
    }

    /**
     * @throws NullEmailAndPhoneNumberException
     */
    public static function restore(
        Id $id,
        UserId $userId,
        ?Email $email,
        ?PhoneNumber $phoneNumber,
        Subject $subject,
        Content $content,
        Status $status
    ): self {
        $notification = new self($id, $userId, $subject, $content, $email, $phoneNumber);
        $notification->status = $status;

        return $notification;
    }
}
