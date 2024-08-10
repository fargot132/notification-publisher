<?php

declare(strict_types=1);

namespace App\NotificationPublisher\UserInterface\Controller;

use App\NotificationPublisher\Application\Command\CreateNotificationCommand;
use App\NotificationPublisher\Application\Throttling\ThrottlingService;
use App\SharedKernel\Application\MessageBus\CommandBusInterface;
use App\SharedKernel\Application\Uuid\UuidServiceInterface;
use InvalidArgumentException;
use JsonException;
use OpenApi\Attributes as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Routing\Annotation\Route;

class CreateNotification extends AbstractController
{
    public function __construct(
        private CommandBusInterface $commandBus,
        private UuidServiceInterface $uuidService,
        private ThrottlingService $throttlingService
    ) {
    }

    #[Route('/api/notification', name: 'send_notification', methods: ['POST'], format: 'json')]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            description: 'Send notification',
            type: 'object',
            example: [
                'userId' => 'c88332db-edfa-4b36-a03f-f27f6945f77e',
                'subject' => 'Notification subject',
                'content' => 'Notification content',
                'email' => 'test@mail.com',
                'phone' => '+1234567890'
            ]
        )
    )]
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $command = $this->createCommandFromRequest($request);
            $this->commandBus->command($command);
        } catch (InvalidArgumentException|JsonException|BadRequestHttpException $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (HandlerFailedException $e) {
            if ($e->getPrevious() === null) {
                return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            return $this->json(['error' => $e->getPrevious()->getMessage()], Response::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['id' => $command->id], Response::HTTP_CREATED);
    }

    /**
     * @throws JsonException
     */
    private function createCommandFromRequest(Request $request): CreateNotificationCommand
    {
        $body = $request->getContent();
        if ($body === null) {
            throw new BadRequestHttpException('Request body is required');
        }

        $parameters = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

        $userId = $parameters['userId'] ?? null;
        if ($userId === null) {
            throw new BadRequestHttpException('userId is required');
        }

        if ($this->throttlingService->isThrottled($userId)) {
            throw new BadRequestHttpException('User is throttled');
        }

        $subject = $parameters['subject'] ?? null;
        if ($subject === null) {
            throw new BadRequestHttpException('title is required');
        }

        $content = $parameters['content'] ?? null;
        if ($content === null) {
            throw new BadRequestHttpException('content is required');
        }

        $email = $parameters['email'] ?? null;
        if ($email === null) {
            throw new BadRequestHttpException('email is required');
        }

        $phone = $parameters['phone'] ?? null;
        if ($phone === null) {
            throw new BadRequestHttpException('phone is required');
        }

        return new CreateNotificationCommand(
            $this->uuidService->generate(), $userId, $subject, $content, $email, $phone
        );
    }
}
