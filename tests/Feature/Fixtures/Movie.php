<?php

namespace Tests\Feature\Fixtures;

use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\BelongsTo;

class Movie extends Model
{
    protected $table = 'movies';

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
