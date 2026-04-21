<?php

namespace App\Domain\Chirp\Events;

readonly class ChirpCreated
{
    public function __construct(
        public int $chirpId,
        public int $userId,
    ) {
    }
}
