<?php

namespace App\Application\Chirp\Commands;

readonly class UpdateChirpCommand
{
    public function __construct(
        public int $userId,
        public int $chirpId,
        public string $title,
        public ?string $description,
    ) {
    }
}
