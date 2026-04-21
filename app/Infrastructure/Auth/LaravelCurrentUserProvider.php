<?php

namespace App\Infrastructure\Auth;

use App\Interfaces\Http\Auth\CurrentUserProvider;

class LaravelCurrentUserProvider implements CurrentUserProvider
{
    public function id(): int
    {
        return (int) auth()->id();
    }
}
