<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\SetNotificationPendingStatusCommand;
use App\NotificationPublisher\Infrastructure\Persistence\NotificationRepository;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class SetNotificationPendingStatusCommandHandler
{
    public function __construct(
        private NotificationRepository $notificationRepository,
        private EventBusInterface $eventBus
    ) {
    }

    public function __invoke(SetNotificationPendingStatusCommand $command): void
    {
        $notification = $this->notificationRepository->get($command->id);
        if ($notification === null) {
            throw new \RuntimeException('Notification not found');
        }
        $notification->setPendingStatus();
        $this->notificationRepository->save($notification);
        foreach ($notification->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
