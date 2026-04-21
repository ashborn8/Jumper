<?php

namespace App\Application\Chirp\Handlers\Query;

use App\Application\Chirp\Queries\ListUserChirpsQuery;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Collection;

class ListUserChirpsQueryHandler
{
    public function __construct(
        private readonly ChirpRepositoryInterface $chirpRepository,
    ) {
    }

    public function handle(ListUserChirpsQuery $query): Collection
    {
        return $this->chirpRepository->forUser(new UserId($query->userId));
    }
}
