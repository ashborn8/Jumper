<?php

namespace App\Application\Chirp\Handlers\Command;

use App\Application\Chirp\Commands\UpdateChirpCommand;
use App\Domain\Chirp\Aggregates\ChirpAggregate;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Chirp\ValueObjects\ChirpId;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Facades\Event;

class UpdateChirpCommandHandler
{
    public function __construct(
        private readonly ChirpRepositoryInterface $chirpRepository,
    ) {
    }

    public function handle(UpdateChirpCommand $command): void
    {
        $updated = $this->chirpRepository->updateForUser(
            new ChirpId($command->chirpId),
            new UserId($command->userId),
            $command->title,
            $command->description,
        );

        if ($updated) {
            Event::dispatch(ChirpAggregate::updated($command->chirpId, $command->userId));
        }
    }
}
