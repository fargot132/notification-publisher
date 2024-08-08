<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\AddNotificationRecordCommand;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\NotificationRecord;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Id;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Message;
use App\NotificationPublisher\Domain\Notification\NotificationRepositoryInterface;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddNotificationRecordCommandHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private EventBusInterface $eventBus
    ) {
    }

    public function __invoke(AddNotificationRecordCommand $command): void
    {
        $notification = $this->notificationRepository->get($command->notificationId);
        if ($notification === null) {
            throw new \RuntimeException('Notification not found');
        }

        $notificationRecord = NotificationRecord::create(
            new Id($command->id),
            $command->status,
            $command->channel,
            new Message($command->message)
        );

        $notification->addNotificationRecord($notificationRecord);
        $this->notificationRepository->save($notification);
        foreach ($notification->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
