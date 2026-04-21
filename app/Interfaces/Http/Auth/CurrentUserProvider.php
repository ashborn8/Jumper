<?php

namespace App\Interfaces\Http\Auth;

interface CurrentUserProvider
{
    public function id(): int;
}
