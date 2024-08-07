<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Notifier;

use App\NotificationPublisher\Domain\Event\NotificationCreated;
use App\NotificationPublisher\Domain\NotificationReadRepositoryInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationCreatedEventHandler
{
    public function __construct(
        private NotificationReadRepositoryInterface $notificationReadRepository,
        private LoggerInterface $logger,
        private NotifierService $notificationSenderService
    ) {
    }

    public function __invoke(NotificationCreated $event): void
    {
        $notificationId = $event->aggregateId->value();
        $notification = $this->notificationReadRepository->findById($notificationId);
        if ($notification === null) {
            $this->logger->error('Notification not found', ['notificationId' => $notificationId]);
            return;
        }

        $this->notificationSenderService->send($notification);
    }
}
