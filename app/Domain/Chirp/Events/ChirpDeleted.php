<?php

namespace App\Domain\Chirp\Events;

readonly class ChirpDeleted
{
    public function __construct(
        public int $chirpId,
        public int $userId,
    ) {
    }
}
