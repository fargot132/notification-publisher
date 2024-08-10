<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Throttling;

use App\NotificationPublisher\Application\Query\GetThrottlingMessageCountQuery;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;

class ThrottlingService
{
    public function __construct(
        private int $throttlingLimit,
        private string $throttlingInterval,
        private QueryBusInterface $queryBus
    ) {
    }

    public function isThrottled(string $userId): bool
    {
        $messageCount = $this->queryBus->query(new GetThrottlingMessageCountQuery($userId, $this->throttlingInterval));

        return $messageCount >= $this->throttlingLimit;
    }

}
