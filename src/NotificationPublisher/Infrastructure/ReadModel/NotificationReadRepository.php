<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\ReadModel;

use App\NotificationPublisher\Domain\NotificationReadRepositoryInterface;
use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityManagerInterface;

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
            ['id' => $id]
        );
        if ($result === false) {
            return null;
        }

        return new NotificationReadDto(
            $result[0]['id'],
            $result[0]['user_id'],
            $result[0]['email'],
            $result[0]['phone_number'],
            $result[0]['subject'],
            $result[0]['content'],
            $result[0]['status'],
            $result[0]['created_at']
        );
    }
}
