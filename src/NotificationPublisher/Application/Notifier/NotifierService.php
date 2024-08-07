<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Application\Notifier;

use App\NotificationPublisher\Application\Enum\Channel;
use App\NotificationPublisher\Application\NotificationSenderInterface;
use App\NotificationPublisher\Infrastructure\ReadModel\Dto\NotificationReadDto;

class NotifierService
{
    private array $channels = [];

    private string $mode = 'default';

    public function __construct(
        private NotificationSenderInterface $notificationSender,
        string $channel
    ) {
        $this->channelParser($channel);
    }

    public function send(NotificationReadDto $dto): void
    {
        $this->notificationSender->send($dto, 'sms');
    }

    public function channelParser(string $channel): void
    {
        $channel = preg_replace('/\s+/', '', $channel);
        if (empty($channel)) {
            return;
        }
        $channel = strtolower($channel);

        if (str_contains($channel, 'failover')) {
            $this->channels = explode(',', str_replace(['failover(', ')'], '', $channel));
            $this->mode = 'failover';
        } elseif (str_contains($channel, 'concurrent')) {
            $this->channels = explode(',', str_replace(['concurrent(', ')'], '', $channel));
            $this->mode = 'concurrent';
        } else {
            $this->channels = explode(',', $channel);
        }

        $this->channels = array_unique($this->channels);

        foreach ($this->channels as $ch) {
            if (Channel::tryFrom($ch) === null) {
                throw new \InvalidArgumentException('Invalid channel');
            }
        }
    }
}
