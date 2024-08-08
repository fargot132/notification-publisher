<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Domain\Notification;

use App\NotificationPublisher\Domain\Notification\ValueObject\Content;
use App\NotificationPublisher\Domain\Notification\ValueObject\Email;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use App\NotificationPublisher\Domain\Notification\ValueObject\PhoneNumber;
use App\NotificationPublisher\Domain\Notification\ValueObject\Subject;
use App\NotificationPublisher\Domain\Notification\ValueObject\UserId;
use InvalidArgumentException;

class NotificationFactory
{
    /**
     * @throws InvalidArgumentException
     */
    public function create(
        string $id,
        string $userId,
        string $subject,
        string $content,
        string $email,
        string $phone
    ): Notification {
        return Notification::create(
            new Id($id),
            new UserId($userId),
            new Subject($subject),
            new Content($content),
            new Email($email),
            new PhoneNumber($phone)
        );
    }
}
