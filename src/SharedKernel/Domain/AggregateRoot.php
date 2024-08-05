<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain;

abstract class AggregateRoot
{
    /** @var DomainEvent[] */
    private array $events = [];

    final public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    final protected function raise(DomainEvent $event): void
    {
        $this->events[] = $event;
    }
}
