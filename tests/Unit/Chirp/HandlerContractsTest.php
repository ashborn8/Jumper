<?php

use App\Application\Chirp\Commands\CreateChirpCommand;
use App\Application\Chirp\Commands\DeleteChirpCommand;
use App\Application\Chirp\Commands\UpdateChirpCommand;
use App\Application\Chirp\DTOs\ChirpView;
use App\Application\Chirp\Handlers\Command\CreateChirpCommandHandler;
use App\Application\Chirp\Handlers\Command\DeleteChirpCommandHandler;
use App\Application\Chirp\Handlers\Command\UpdateChirpCommandHandler;
use App\Application\Chirp\Handlers\Query\ListUserChirpsQueryHandler;
use App\Application\Chirp\Queries\ListUserChirpsQuery;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Chirp\ValueObjects\ChirpId;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Collection;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

it('passes value objects to create command repository contract', function () {
    $repo = new class implements ChirpRepositoryInterface
    {
        public ?UserId $userId = null;
        public ?string $title = null;
        public ?string $description = null;

        public function forUser(UserId $userId): Collection
        {
            return collect();
        }

        public function recent(): Collection
        {
            return collect();
        }

        public function timeline(UserId $userId, string $feed): Collection
        {
            return collect();
        }

        public function createForUser(UserId $userId, string $title, ?string $description, ?string $imagePath): int
        {
            $this->userId = $userId;
            $this->title = $title;
            $this->description = $description;

            return 1;
        }

        public function updateForUser(ChirpId $chirpId, UserId $userId, string $title, ?string $description): bool
        {
            return true;
        }

        public function deleteForUser(ChirpId $chirpId, UserId $userId): bool
        {
            return true;
        }
    };

    $handler = new CreateChirpCommandHandler($repo);
    $handler->handle(new CreateChirpCommand(7, 'Titulo', 'Descripcion', null));

    expect($repo->userId?->value())->toBe(7)
        ->and($repo->title)->toBe('Titulo')
        ->and($repo->description)->toBe('Descripcion');
});

it('passes value objects to update and delete repository contracts', function () {
    $repo = new class implements ChirpRepositoryInterface
    {
        public ?ChirpId $updatedChirpId = null;
        public ?UserId $updatedUserId = null;
        public ?ChirpId $deletedChirpId = null;
        public ?UserId $deletedUserId = null;

        public function forUser(UserId $userId): Collection
        {
            return collect();
        }

        public function recent(): Collection
        {
            return collect();
        }

        public function timeline(UserId $userId, string $feed): Collection
        {
            return collect();
        }

        public function createForUser(UserId $userId, string $title, ?string $description, ?string $imagePath): int
        {
            return 1;
        }

        public function updateForUser(ChirpId $chirpId, UserId $userId, string $title, ?string $description): bool
        {
            $this->updatedChirpId = $chirpId;
            $this->updatedUserId = $userId;

            return true;
        }

        public function deleteForUser(ChirpId $chirpId, UserId $userId): bool
        {
            $this->deletedChirpId = $chirpId;
            $this->deletedUserId = $userId;

            return true;
        }
    };

    (new UpdateChirpCommandHandler($repo))->handle(new UpdateChirpCommand(
        userId: 5,
        chirpId: 12,
        title: 'Nuevo',
        description: null,
    ));

    (new DeleteChirpCommandHandler($repo))->handle(new DeleteChirpCommand(
        userId: 5,
        chirpId: 12,
    ));

    expect($repo->updatedUserId?->value())->toBe(5)
        ->and($repo->updatedChirpId?->value())->toBe(12)
        ->and($repo->deletedUserId?->value())->toBe(5)
        ->and($repo->deletedChirpId?->value())->toBe(12);
});

it('returns chirp views for list user query handler', function () {
    $repo = new class implements ChirpRepositoryInterface
    {
        public function forUser(UserId $userId): Collection
        {
            return collect([
                new ChirpView(1, 't', 'd', null, null, $userId->value(), 'User'),
            ]);
        }

        public function recent(): Collection
        {
            return collect();
        }

        public function timeline(UserId $userId, string $feed): Collection
        {
            return collect();
        }

        public function createForUser(UserId $userId, string $title, ?string $description, ?string $imagePath): int
        {
            return 1;
        }

        public function updateForUser(ChirpId $chirpId, UserId $userId, string $title, ?string $description): bool
        {
            return true;
        }

        public function deleteForUser(ChirpId $chirpId, UserId $userId): bool
        {
            return true;
        }
    };

    $result = (new ListUserChirpsQueryHandler($repo))->handle(new ListUserChirpsQuery(9));

    expect($result)->toHaveCount(1)
        ->and($result->first())->toBeInstanceOf(ChirpView::class)
        ->and($result->first()->userId)->toBe(9);
});
