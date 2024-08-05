<?php

declare(strict_types=1);

namespace App\NotificationPublisher\UserInterface\Controller;

use App\NotificationPublisher\Domain\Exception\NullEmailAndPhoneNumberException;
use App\NotificationPublisher\Domain\NotificationFactory;
use App\NotificationPublisher\UserInterface\Dto\SendNotificationDto;
use App\SharedKernel\Infrastructure\Uuid\UuidServiceInterface;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SendNotification
{
    public function __construct(
        private NotificationFactory $notificationFactory,
        private UuidServiceInterface $uuidService
    ) {
    }

    #[Route('/api/notification', name: 'send_notification', methods: ['POST'])]
    public function __invoke(Request $request): void
    {
        $dto = $this->createDtoFromRequest($request);
        $id = $this->uuidService->generate();
        try {
            $notification = $this->notificationFactory->create(
                $id,
                $dto->userId,
                $dto->subject,
                $dto->content,
                $dto->email,
                $dto->phone
            );
        } catch (InvalidArgumentException|NullEmailAndPhoneNumberException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    private function createDtoFromRequest(Request $request): SendNotificationDto
    {
        $userId = $request->get('userId');
        if ($userId === null) {
            throw new BadRequestHttpException('userId is required');
        }

        $subject = $request->get('subject');
        if ($subject === null) {
            throw new BadRequestHttpException('title is required');
        }

        $content = $request->get('content');
        if ($content === null) {
            throw new BadRequestHttpException('content is required');
        }

        $email = $request->get('email');
        $phone = $request->get('phone');

        return new SendNotificationDto($userId, $subject, $content, $email, $phone);
    }
}
