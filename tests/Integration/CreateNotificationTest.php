<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use App\NotificationPublisher\Application\Command\CreateNotificationCommand;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Infrastructure\EventBus\EventBus;
use App\Tests\TestCase\IntegrationTestCase;
use DateTime;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\Uid\Uuid;

class CreateNotificationTest extends IntegrationTestCase
{
    private MockObject $eventBus;

    protected function setUp(): void
    {
        parent::setUp();
        $this->eventBus = $this->createMock(EventBus::class);
        $container = self::getContainer();
        $container->set(EventBusInterface::class, $this->eventBus);
    }

    public function testCreateNotification(): void
    {
        $id = (string)Uuid::v4();
        $userId = (string)Uuid::v4();
        $command = new CreateNotificationCommand(
            $id,
            $userId,
            'Test notification',
            'Test notification content',
            'test@domain.com',
            '+1234567890'
        );
        $commandBus = self::getContainer()->get(CommandBusInterface::class);
        $this->eventBus->expects(self::once())->method('dispatch');
        $commandBus->command($command);

        $result = $this->connection->fetchAllAssociative(
            'SELECT * FROM notification WHERE id = :id',
            ['id' => $id],
            ['id' => 'uuid']
        );

        self::assertCount(1, $result);
        self::assertSame($id, (string)Uuid::fromBinary($result[0]['id']));
        self::assertSame($userId, (string)Uuid::fromBinary($result[0]['user_id_value']));
        self::assertSame('Test notification', $result[0]['subject_value']);
        self::assertSame('Test notification content', $result[0]['content_value']);
        self::assertSame('test@domain.com', $result[0]['email_value']);
        self::assertSame('+1234567890', $result[0]['phone_number_value']);
        self::assertSame('new', $result[0]['status']);
        self::assertSame(0, $result[0]['retry_count_value']);
        self::assertTrue($this->isValidDateTime($result[0]['created_at']));
        self::assertTrue($this->isValidDateTime($result[0]['updated_at']));
    }

    private function isValidDateTime(string $dateTime, string $format = 'Y-m-d H:i:s'): bool
    {
        $date = DateTime::createFromFormat($format, $dateTime);
        return $date && $date->format($format) === $dateTime;
    }
}
