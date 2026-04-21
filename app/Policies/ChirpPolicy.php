<?php

namespace App\Policies;

use App\Models\Chirp;
use App\Models\User;

class ChirpPolicy
{
    /**
     * Determine whether the user can update the chirp.
     */
    public function update(User $user, Chirp $chirp): bool
    {
        return $user->id === $chirp->user_id;
    }

    /**
     * Determine whether the user can delete the chirp.
     */
    public function delete(User $user, Chirp $chirp): bool
    {
        return $user->id === $chirp->user_id;
    }
}
