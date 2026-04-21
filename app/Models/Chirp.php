<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'description', 'image_path'])]
class Chirp extends Model
{
    /**
     * Get the user that created the chirp.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
