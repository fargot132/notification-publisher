<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain;

use App\NotificationPublisher\Domain\Exception\NullEmailAndPhoneNumberException;
use App\NotificationPublisher\Domain\ValueObject\Content;
use App\NotificationPublisher\Domain\ValueObject\Email;
use App\NotificationPublisher\Domain\ValueObject\Id;
use App\NotificationPublisher\Domain\ValueObject\PhoneNumber;
use App\NotificationPublisher\Domain\ValueObject\Subject;
use App\NotificationPublisher\Domain\ValueObject\UserId;
use InvalidArgumentException;

class NotificationFactory
{
    /**
     * @throws NullEmailAndPhoneNumberException
     * @throws InvalidArgumentException
     */
    public function create(
        string $id,
        string $userId,
        string $subject,
        string $content,
        ?string $email,
        ?string $phone
    ): Notification {
        return Notification::create(
            new Id($id),
            new UserId($userId),
            new Subject($subject),
            new Content($content),
            $email ? new Email($email) : null,
            $phone ? new PhoneNumber($phone) : null
        );
    }
}
