<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\ReadModel;

use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
use App\NotificationPublisher\Domain\Notification\ValueObject\Status;
use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Types\UuidType;
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
            $result['created_at']
        );
    }

    public function getNotificationIdsForRetry(\DateInterval $interval): array
    {
        $status = Status::PENDING->value;
        $updatedAt = (new \DateTimeImmutable())->sub($interval)->format('Y-m-d H:i:s');
        $results = $this->connection->fetchAllAssociative(
            'SELECT id FROM notification WHERE status = :status AND updated_at < :updated_at',
            [
                'status' => $status,
                'updated_at' => $updatedAt
            ]
        );

        return array_map(
            fn($result) => (string)Uuid::fromBinary($result['id']),
            $results
        );
    }
}
