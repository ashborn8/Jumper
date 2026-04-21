<?php

namespace App\Application\Chirp\Queries;

readonly class ListUserChirpsQuery
{
    public function __construct(
        public int $userId,
    ) {
    }
}
