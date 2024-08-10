<?php

declare(strict_types=1);

namespace App\Tests\Unit\NotificationPublisher\Application\Throttling;

use PHPUnit\Framework\TestCase;
use App\NotificationPublisher\Application\Throttling\ThrottlingService;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;

class ThrottlingServiceTest extends TestCase
{
    private $queryBusMock;
    private $throttlingService;

    protected function setUp(): void
    {
        $this->queryBusMock = $this->createMock(QueryBusInterface::class);
        $this->throttlingService = new ThrottlingService(10, '1 hour', $this->queryBusMock);
    }

    public function testIsThrottledWhenMessageCountBelowLimit(): void
    {
        $this->queryBusMock->method('query')
            ->willReturn(5);

        $result = $this->throttlingService->isThrottled('user123');
        $this->assertFalse($result);
    }

    public function testIsThrottledWhenMessageCountEqualsLimit(): void
    {
        $this->queryBusMock->method('query')
            ->willReturn(10);

        $result = $this->throttlingService->isThrottled('user123');
        $this->assertTrue($result);
    }

    public function testIsThrottledWhenMessageCountAboveLimit(): void
    {
        $this->queryBusMock->method('query')
            ->willReturn(15);

        $result = $this->throttlingService->isThrottled('user123');
        $this->assertTrue($result);
    }
}
