<?php

namespace App\Domain\Chirp\Events;

readonly class ChirpUpdated
{
    public function __construct(
        public int $chirpId,
        public int $userId,
    ) {
    }
}
