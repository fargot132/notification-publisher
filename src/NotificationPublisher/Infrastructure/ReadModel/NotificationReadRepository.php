<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\ReadModel;

use App\NotificationPublisher\Application\Dto\NotificationReadDto;
use App\NotificationPublisher\Application\Dto\NotificationRecordReadDto;
use App\NotificationPublisher\Application\NotificationReadRepositoryInterface;
use App\NotificationPublisher\Domain\Notification\ValueObject\Status;
use DateInterval;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Uid\Uuid;

class NotificationReadRepository implements NotificationReadRepositoryInterface
{
    private Connection $connection;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->connection = $entityManager->getConnection();
    }

    /**
     * @throws Exception
     */
    public function findById(string $id): ?NotificationReadDto
    {
        $result = $this->connection->fetchAssociative(
            'SELECT * FROM notification WHERE id = :id',
            ['id' => $id],
            ['id' => 'uuid']
        );
        if ($result === false) {
            return null;
        }

        return new NotificationReadDto(
            (string)Uuid::fromBinary($result['id']),
            (string)Uuid::fromBinary($result['user_id_value']),
            $result['email_value'],
            $result['phone_number_value'],
            $result['subject_value'],
            $result['content_value'],
            $result['status'],
            $result['created_at'],
            $result['updated_at'],
            (int)$result['retry_count_value']
        );
    }

    /**
     * @return array<string>
     * @throws Exception
     */
    public function getNotificationIdsForRetry(string $interval): array
    {
        $updatedAt = (new \DateTimeImmutable())->sub(DateInterval::createFromDateString($interval));

        $results = $this->connection->fetchAllAssociative(
            'SELECT id FROM notification WHERE status = :status AND updated_at < :updated_at',
            [
                'status' => Status::PENDING->value,
                'updated_at' => $updatedAt
            ],
            ['updated_at' => 'datetime']
        );

        return array_map(
            static fn($result) => (string)Uuid::fromBinary($result['id']),
            $results
        );
    }

    public function getThrottlingMessageCount(string $userId, string $interval): int
    {
        $createdAt = (new \DateTimeImmutable())->sub(DateInterval::createFromDateString($interval));

        $result = $this->connection->fetchAssociative(
            'SELECT COUNT(id) as count FROM notification WHERE user_id_value = :user_id AND created_at > :created_at',
            [
                'user_id' => $userId,
                'created_at' => $createdAt
            ],
            ['created_at' => 'datetime', 'user_id' => 'uuid']
        );

        return (int)$result['count'];
    }

    public function getNotificationRecordsById(string $id): array
    {
        $results = $this->connection->fetchAllAssociative(
            'SELECT * FROM notification_record WHERE notification_id = :notification_id',
            ['notification_id' => $id],
            ['notification_id' => 'uuid']
        );

        return array_map(
            static fn($result) => new NotificationRecordReadDto(
                (string)Uuid::fromBinary($result['id']),
                (string)Uuid::fromBinary($result['notification_id']),
                $result['status'],
                $result['channel'],
                $result['message_value'],
                $result['created_at']
            ),
            $results
        );
    }

    public function getNotificationsByUserId(string $userId, int $limit, int $offset): array
    {
        $results = $this->connection->fetchAllAssociative(
            'SELECT * FROM notification WHERE user_id_value = :user_id LIMIT :limit OFFSET :offset',
            ['user_id' => $userId, 'limit' => $limit, 'offset' => $offset],
            ['user_id' => 'uuid', 'limit' => 'integer', 'offset' => 'integer']
        );

        return array_map(
            static fn($result) => new NotificationReadDto(
                (string)Uuid::fromBinary($result['id']),
                (string)Uuid::fromBinary($result['user_id_value']),
                $result['email_value'],
                $result['phone_number_value'],
                $result['subject_value'],
                $result['content_value'],
                $result['status'],
                $result['created_at'],
                $result['updated_at'],
                (int)$result['retry_count_value']
            ),
            $results
        );
    }
}
