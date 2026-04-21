<?php

namespace App\Application\Chirp\Listeners;

use App\Application\Chirp\Support\TimelineCache;
use App\Domain\Chirp\Events\ChirpCreated;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class ProcessChirpCreated implements ShouldQueue
{
    public function handle(ChirpCreated $event): void
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

        Log::info('chirp.created', [
            'chirp_id' => $event->chirpId,
            'user_id' => $event->userId,
        ]);
    }
}
