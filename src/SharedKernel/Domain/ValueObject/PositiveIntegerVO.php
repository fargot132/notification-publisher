<?php

declare(strict_types=1);

namespace App\SharedKernel\Domain\ValueObject;

use InvalidArgumentException;

class PositiveIntegerVO extends IntegerVO
{
    public function __construct(int $value)
    {
        if ($value < 0) {
            throw new InvalidArgumentException('Value must be positive');
        }

        parent::__construct($value);
    }
}
