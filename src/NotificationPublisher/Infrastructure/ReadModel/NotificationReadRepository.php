<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\ReadModel;

use App\NotificationPublisher\Domain\Notification\NotificationReadRepositoryInterface;
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
}
