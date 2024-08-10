<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\CreateNotificationCommand;
use App\NotificationPublisher\Domain\Notification\NotificationFactory;
use App\NotificationPublisher\Domain\Notification\NotificationRepositoryInterface;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class CreateNotificationCommandHandler
{
    public function __construct(
        private NotificationFactory $notificationFactory,
        private NotificationRepositoryInterface $notificationRepository,
        private EventBusInterface $eventBus,
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
