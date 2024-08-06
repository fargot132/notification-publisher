<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\MessageBus;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

class CommandBus
{
    use HandleTrait;

    public function __construct(MessageBusInterface $commandBus)
    {
        $this->messageBus = $commandBus;
    }

    public function command(object $command): mixed
    {
        return $this->handle($command);
    }
}
