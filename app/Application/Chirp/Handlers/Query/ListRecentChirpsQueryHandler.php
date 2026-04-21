<?php

namespace App\Application\Chirp\Handlers\Query;

use App\Application\Chirp\Queries\ListRecentChirpsQuery;
use App\Application\Chirp\Support\TimelineCache;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Collection;

class ListRecentChirpsQueryHandler
{
    public function __construct(
        private readonly ChirpRepositoryInterface $chirpRepository,
    ) {
    }

    public function handle(ListRecentChirpsQuery $query): Collection
    {
        return TimelineCache::remember(
            $query->userId,
            $query->feed,
            fn () => $this->chirpRepository->timeline(
                new UserId($query->userId),
                $query->feed,
            ),
        );
    }
}
