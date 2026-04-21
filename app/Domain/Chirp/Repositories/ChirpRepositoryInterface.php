<?php

namespace App\Domain\Chirp\Repositories;

use App\Application\Chirp\DTOs\ChirpView;
use App\Domain\Chirp\ValueObjects\ChirpId;
use App\Domain\Shared\ValueObjects\UserId;
use Illuminate\Support\Collection;

interface ChirpRepositoryInterface
{
    /**
     * @return Collection<int, ChirpView>
     */
    public function forUser(UserId $userId): Collection;

    /**
     * @return Collection<int, ChirpView>
     */
    public function recent(): Collection;

    /**
     * @return Collection<int, ChirpView>
     */
    public function timeline(UserId $userId, string $feed): Collection;

    public function createForUser(UserId $userId, string $title, ?string $description, ?string $imagePath): int;

    public function updateForUser(ChirpId $chirpId, UserId $userId, string $title, ?string $description): bool;

    public function deleteForUser(ChirpId $chirpId, UserId $userId): bool;
}
