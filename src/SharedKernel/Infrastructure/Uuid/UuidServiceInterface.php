<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Uuid;

interface UuidServiceInterface
{
    public function generate(): string;
}
