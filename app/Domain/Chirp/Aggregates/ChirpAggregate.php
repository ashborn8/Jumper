<?php

namespace App\Domain\Chirp\Aggregates;

use App\Domain\Chirp\Events\ChirpCreated;
use App\Domain\Chirp\Events\ChirpDeleted;
use App\Domain\Chirp\Events\ChirpUpdated;

class ChirpAggregate
{
    /**
     * Record a chirp creation domain event.
     */
    public static function created(int $chirpId, int $userId): ChirpCreated
    {
        return new ChirpCreated($chirpId, $userId);
    }

    /**
     * Record a chirp update domain event.
     */
    public static function updated(int $chirpId, int $userId): ChirpUpdated
    {
        return new ChirpUpdated($chirpId, $userId);
    }

    /**
     * Record a chirp deletion domain event.
     */
    public static function deleted(int $chirpId, int $userId): ChirpDeleted
    {
        return new ChirpDeleted($chirpId, $userId);
    }
}
