<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\EventHandler;

use App\NotificationPublisher\Application\Notifier\NotifierService;
use App\NotificationPublisher\Application\Query\GetNotificationByIdQuery;
use App\NotificationPublisher\Domain\Notification\Event\NotificationRetriedSending;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationRetriedSendingEventHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private NotifierService $notifierService,
        private QueryBusInterface $queryBus
    ) {
    }

    public function __invoke(NotificationRetriedSending $event): void
    {
        $notificationId = $event->aggregateId->value();
        $notification = $this->queryBus->query(new GetNotificationByIdQuery($notificationId));
        if ($notification === null) {
            $this->logger->error('Notification not found', ['notificationId' => $notificationId]);
            return;
        }

        $this->notifierService->send($notification);
    }
}
