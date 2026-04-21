<?php

namespace App\Domain\Shared\ValueObjects;

readonly class UserId
{
    public function __construct(
        private int $value,
    ) {
    }

    public function value(): int
    {
        return $this->value;
    }
}
