<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\CommandHandler;

use App\NotificationPublisher\Application\Command\RetrySendingCommand;
use App\NotificationPublisher\Domain\Notification\NotificationRepositoryInterface;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class RetrySendingCommandHandler
{
    public function __construct(
        private NotificationRepositoryInterface $notificationRepository,
        private EventBusInterface $eventBus
    ) {
    }

    public function __invoke(RetrySendingCommand $command): void
    {
        $notification = $this->notificationRepository->get($command->id);
        if ($notification === null) {
            throw new \RuntimeException('Notification not found');
        }
        $notification->retrySending();
        $this->notificationRepository->save($notification);
        foreach ($notification->pullEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
