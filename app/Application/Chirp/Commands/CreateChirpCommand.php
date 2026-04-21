<?php

namespace App\Application\Chirp\Commands;

readonly class CreateChirpCommand
{
    public function __construct(
        public int $userId,
        public string $title,
        public ?string $description,
        public ?string $imagePath,
    ) {
    }
}
