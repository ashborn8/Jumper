<?php

namespace App\Application\Chirp\Handlers\Command;

use App\Application\Chirp\Commands\CreateChirpCommand;
use App\Domain\Chirp\Aggregates\ChirpAggregate;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Facades\Event;

class CreateChirpCommandHandler
{
    public function __construct(
        private readonly ChirpRepositoryInterface $chirpRepository,
    ) {
    }

    public function handle(CreateChirpCommand $command): void
    {
        $chirpId = $this->chirpRepository->createForUser(
            new UserId($command->userId),
            $command->title,
            $command->description,
            $command->imagePath,
        );

        Event::dispatch(ChirpAggregate::created($chirpId, $command->userId));
    }
}
