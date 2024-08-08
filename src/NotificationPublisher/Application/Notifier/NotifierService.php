<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Notifier;

use App\NotificationPublisher\Application\NotificationSenderInterface;
use App\NotificationPublisher\Domain\Notification\NotificationRecord\ValueObject\Channel;
use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;
use InvalidArgumentException;

class NotifierService
{
    private array $channels = [];

    private string $mode = 'concurrent';

    public function __construct(
        private NotificationSenderInterface $notificationSender,
        string $channel
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
        foreach ($this->channels as $channel) {
            $this->notificationSender->send($dto, $channel);
        }
    }

    private function sendFailover(NotificationReadDto $dto): void
    {
        foreach ($this->channels as $channel) {
            try {
                $this->notificationSender->send($dto, $channel);
                return;
            } catch (\Exception) {
                continue;
            }
        }
    }
}
