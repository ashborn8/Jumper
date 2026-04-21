<?php

namespace App\Application\Chirp\Listeners;

use App\Application\Chirp\Support\TimelineCache;
use App\Domain\Chirp\Events\ChirpDeleted;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ProcessChirpDeleted implements ShouldQueue
{
    public function handle(ChirpDeleted $event): void
    {
        $affectedUserIds = User::query()
            ->find($event->userId)
            ?->followers()
            ->pluck('users.id')
            ->push($event->userId)
            ->unique()
            ->values()
            ->all() ?? [$event->userId];

        foreach ($affectedUserIds as $userId) {
            TimelineCache::flushForUser((int) $userId);
        }

        Log::info('chirp.deleted', [
            'chirp_id' => $event->chirpId,
            'user_id' => $event->userId,
        ]);
    }
}
