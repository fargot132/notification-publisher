<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Notifier;

use App\NotificationPublisher\Application\NotificationSenderInterface;
use App\NotificationPublisher\Domain\Notification\Event\NotificationAllChannelsFailed;
use App\NotificationPublisher\Domain\Notification\Event\NotificationChannelFailed;
use App\NotificationPublisher\Domain\Notification\Event\NotificationSent;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\NotificationPublisher\Domain\Notification\ValueObject\Id;
use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;
use App\SharedKernel\Application\EventBus\EventBusInterface;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;

class NotifierService
{
    private array $channels = [];

    private string $mode = 'concurrent';

    public function __construct(
        string $channel,
        private NotificationSenderInterface $notificationSender,
        private EventBusInterface $eventBus,
        private LoggerInterface $logger
    ) {
        $this->channelParser($channel);
    }

    public function send(NotificationReadDto $dto): void
    {
        if ($this->mode === 'concurrent') {
            $this->sendConcurrent($dto);
        } else {
            $this->sendFailover($dto);
        }
        $this->notificationSender->send($dto, 'sms');
    }

    private function channelParser(string $channelConfig): void
    {
        $channelConfig = preg_replace('/\s+/', '', $channelConfig);
        if (empty($channelConfig)) {
            return;
        }
        $channelConfig = strtolower($channelConfig);

        if (str_contains($channelConfig, 'failover')) {
            $this->channels = explode(',', str_replace(['failover(', ')'], '', $channelConfig));
            $this->mode = 'failover';
        } else {
            $this->channels = explode(',', $channelConfig);
        }

        $this->channels = array_unique($this->channels);

        foreach ($this->channels as $channel) {
            if (Channel::tryFrom($channel) === null) {
                throw new InvalidArgumentException('Invalid notifier channel configuration');
            }
        }
    }

    private function sendConcurrent(NotificationReadDto $dto): void
    {
        $atLeastOneSuccess = false;
        foreach ($this->channels as $channel) {
            try {
                $this->notificationSender->send($dto, $channel);
                $this->success($dto->id, $channel);
                $atLeastOneSuccess = true;
            } catch (\Exception $e) {
                $this->channelFailed($dto->id, $channel);
                continue;
            }
        }

        if (!$atLeastOneSuccess && !empty($this->channels)) {
            $this->allChannelsFailed($dto->id);
        }
    }

    private function sendFailover(NotificationReadDto $dto): void
    {
        foreach ($this->channels as $channel) {
            try {
                $this->notificationSender->send($dto, $channel);
                $this->success($dto->id, $channel);

                return;
            } catch (\Exception) {
                $this->channelFailed($dto->id, $channel);
                continue;
            }
        }
        $this->allChannelsFailed($dto->id);
    }

    private function success(string $id, string $channel): void
    {
        $this->logger->info('Notification sent', ['id' => $id, 'channel' => $channel]);
        $this->eventBus->dispatch(new NotificationSent(new Id($id), Channel::from($channel)));
    }

    private function channelFailed(string $id, string $channel): void
    {
        $this->logger->error('Notification channel failed', ['id' => $id, 'channel' => $channel]);
        $this->eventBus->dispatch(new NotificationChannelFailed(new Id($id), Channel::from($channel)));
    }

    private function allChannelsFailed(string $id): void
    {
        $this->logger->error('Notification all channels failed', ['id' => $id]);
        $this->eventBus->dispatch(new NotificationAllChannelsFailed(new Id($id)));
    }
}
