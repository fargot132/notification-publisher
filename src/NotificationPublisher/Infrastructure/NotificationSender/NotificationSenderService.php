<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\NotificationSender;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;
use App\NotificationPublisher\Application\NotificationSenderInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;

class NotificationSenderService implements NotificationSenderInterface
{
    public function __construct(private NotifierInterface $notifier)
    {
    }

    public function send(NotificationReadDto $dto, string $channel): void
    {
        $notification = (new Notification($dto->subject, [$channel]))
            ->content($dto->content);
        $recipient = new Recipient($dto->email, $dto->phoneNumber);
        $this->notifier->send($notification, $recipient);
    }
}
