<?php

namespace App\Application\Chirp\Commands;

readonly class DeleteChirpCommand
{
    public function __construct(
        public int $userId,
        public int $chirpId,
    ) {
    }
}
