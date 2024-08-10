<?php

declare(strict_types=1);

namespace App\NotificationPublisher\UserInterface\Controller;

use App\NotificationPublisher\Application\Query\GetNotificationByIdQuery;
use App\NotificationPublisher\Application\Query\GetNotificationRecordsByIdQuery;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;
use App\SharedKernel\Application\Uuid\UuidServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class GetNotificationInfo extends AbstractController
{
    public function __construct(private QueryBusInterface $queryBus, private UuidServiceInterface $uuidService)
    {
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/notification/{id}', name: 'get_notification', methods: ['GET'], format: 'json')]
    public function __invoke(string $id): JsonResponse
    {
        if (!$this->uuidService->validate($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $notification = $this->queryBus->query(new GetNotificationByIdQuery($id));
        if ($notification === null) {
            return $this->json(['error' => 'Notification not found'], Response::HTTP_NOT_FOUND);
        }

        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers);
        $notificationArray = $serializer->normalize($notification);
        $notificationArray['records'] = $this->queryBus->query(new GetNotificationRecordsByIdQuery($id));

        return $this->json($notificationArray);
    }
}
