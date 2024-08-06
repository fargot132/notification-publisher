<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\CreateNotificationCommand;
use App\NotificationPublisher\Domain\NotificationFactory;
use App\NotificationPublisher\Domain\NotificationRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class CreateNotificationCommandHandler
{
    public function __construct(
        private NotificationFactory $notificationFactory,
        private NotificationRepositoryInterface $notificationRepository,
        private MessageBusInterface $eventBus
    ) {
    }
    public function __invoke(CreateNotificationCommand $command): void
    {
        $notification = $this->notificationFactory->create(
            $command->id,
            $command->userId,
            $command->subject,
            $command->content,
            $command->email,
            $command->phone

        );
        $this->notificationRepository->save($notification);
        foreach ($notification->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
