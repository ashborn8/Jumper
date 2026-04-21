<?php

namespace App\Domain\Chirp\ValueObjects;

readonly class ChirpId
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
