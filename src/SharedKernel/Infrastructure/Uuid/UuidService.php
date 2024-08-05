<?php

declare(strict_types=1);

namespace App\SharedKernel\Infrastructure\Uuid;

use Symfony\Component\Uid\Uuid;

class UuidService implements UuidServiceInterface
{
    public function generate(): string
    {
        return (string)Uuid::v4();
    }
}
