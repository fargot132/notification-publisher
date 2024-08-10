<?php

declare(strict_types=1);

namespace App\NotificationPublisher\UserInterface\Controller;

use App\NotificationPublisher\Application\Query\GetNotificationsByUserIdQuery;
use App\SharedKernel\Application\MessageBus\QueryBusInterface;
use App\SharedKernel\Application\Uuid\UuidServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

class GetNotificationsByUserId extends AbstractController
{
    public function __construct(private QueryBusInterface $queryBus, private UuidServiceInterface $uuidService)
    {
    }

    #[Route('/api/notification/user/{id}', name: 'get_notification_by_user_id', methods: ['GET'], format: 'json')]
    #[OA\Parameter(
        name: 'id',
        description: 'User ID',
        in: 'path',
        required: true,
        example: 'c88332db-edfa-4b36-a03f-f27f6945f77e'
    )]
    #[OA\Parameter(
        name: 'page',
        description: 'Page number',
        in: 'query',
        required: false,
        example: 1
    )]
    #[OA\Parameter(
        name: 'limit',
        description: 'Items per page',
        in: 'query',
        required: false,
        example: 10
    )]
    public function __invoke(string $id, Request $request): JsonResponse
    {
        if (!$this->uuidService->validate($id)) {
            return $this->json(['error' => 'Invalid UUID'], Response::HTTP_BAD_REQUEST);
        }

        $page = max(1, (int)$request->query->get('page', 1));
        $limit = min(100, max(1, (int)$request->query->get('limit', 10)));
        $offset = ($page - 1) * $limit;

        $query = new GetNotificationsByUserIdQuery($id, $limit, $offset);
        $notifications = $this->queryBus->query($query);

        return $this->json($notifications);
    }
}
