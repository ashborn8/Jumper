<?php

namespace App\Application\Chirp\Queries;

readonly class ListRecentChirpsQuery
{
    public function __construct(
        public int $userId,
        public string $feed = 'fyp',
    ) {
    }
}
