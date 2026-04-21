<?php

namespace App\Application\Chirp\Handlers\Command;

use App\Application\Chirp\Commands\DeleteChirpCommand;
use App\Domain\Chirp\Aggregates\ChirpAggregate;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Chirp\ValueObjects\ChirpId;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Facades\Event;

class DeleteChirpCommandHandler
{
    public function __construct(
        private readonly ChirpRepositoryInterface $chirpRepository,
    ) {
    }

    public function handle(DeleteChirpCommand $command): void
    {
        $deleted = $this->chirpRepository->deleteForUser(
            new ChirpId($command->chirpId),
            new UserId($command->userId),
        );

        if ($deleted) {
            Event::dispatch(ChirpAggregate::deleted($command->chirpId, $command->userId));
        }
    }
}
