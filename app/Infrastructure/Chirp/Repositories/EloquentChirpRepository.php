<?php

namespace App\Infrastructure\Chirp\Repositories;

use App\Application\Chirp\DTOs\ChirpView;
use App\Domain\Chirp\Repositories\ChirpRepositoryInterface;
use App\Domain\Chirp\ValueObjects\ChirpId;
use App\Domain\Shared\ValueObjects\UserId;
use App\Models\Chirp;
use Illuminate\Support\Collection;

class EloquentChirpRepository implements ChirpRepositoryInterface
{
    /**
     * @return Collection<int, ChirpView>
     */
    public function forUser(UserId $userId): Collection
    {
        return Chirp::query()
            ->where('user_id', $userId->value())
            ->with('user:id,name')
            ->latest()
            ->get()
            ->map(fn (Chirp $chirp): ChirpView => $this->toView($chirp));
    }

    /**
     * @return Collection<int, ChirpView>
     */
    public function recent(): Collection
    {
        return Chirp::query()
            ->with('user:id,name')
            ->latest()
            ->get()
            ->map(fn (Chirp $chirp): ChirpView => $this->toView($chirp));
    }

    /**
     * @return Collection<int, ChirpView>
     */
    public function timeline(UserId $userId, string $feed): Collection
    {
        $followingIds = \App\Models\User::query()
            ->find($userId->value())
            ?->following()
            ->pluck('users.id')
            ->all() ?? [];

        $query = Chirp::query()->with('user:id,name');

        if ($feed === 'following') {
            $query->whereIn('user_id', $followingIds);
        } else {
            // "For You": prioritize following + own chirps, then others by recency.
            $priorityIds = array_values(array_unique([...$followingIds, $userId->value()]));
            $idList = empty($priorityIds) ? '0' : implode(',', $priorityIds);
            $query->orderByRaw("CASE WHEN user_id IN ({$idList}) THEN 0 ELSE 1 END ASC");
        }

        return $query
            ->latest()
            ->limit(100)
            ->get()
            ->map(fn (Chirp $chirp): ChirpView => $this->toView($chirp));
    }

    public function createForUser(UserId $userId, string $title, ?string $description, ?string $imagePath): int
    {
        $chirp = Chirp::query()->create([
            'title' => $title,
            'description' => $description,
            'image_path' => $imagePath,
            'user_id' => $userId->value(),
        ]);

        return $chirp->id;
    }

    public function updateForUser(ChirpId $chirpId, UserId $userId, string $title, ?string $description): bool
    {
        $updated = Chirp::query()
            ->whereKey($chirpId->value())
            ->where('user_id', $userId->value())
            ->update([
                'title' => $title,
                'description' => $description,
            ]);

        return $updated > 0;
    }

    public function deleteForUser(ChirpId $chirpId, UserId $userId): bool
    {
        $deleted = Chirp::query()
            ->whereKey($chirpId->value())
            ->where('user_id', $userId->value())
            ->delete();

        return $deleted > 0;
    }

    private function toView(Chirp $chirp): ChirpView
    {
        return new ChirpView(
            id: $chirp->id,
            title: $chirp->title,
            description: $chirp->description,
            imageUrl: $chirp->image_path ? '/storage/'.$chirp->image_path : null,
            createdAt: $chirp->created_at?->toIso8601String(),
            userId: $chirp->user?->id ?? 0,
            userName: $chirp->user?->name ?? 'Usuario',
        );
    }
}
