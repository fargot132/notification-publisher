<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Persistence;

use App\NotificationPublisher\Domain\Notification;
use App\NotificationPublisher\Domain\NotificationRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class NotificationRepository extends ServiceEntityRepository implements NotificationRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    public function save(Notification $notification): void
    {
        $em = $this->getEntityManager();
        $em->persist($notification);
        $em->flush();
    }

    public function get(string $id): ?Notification
    {
        return $this->find($id);
    }
}
