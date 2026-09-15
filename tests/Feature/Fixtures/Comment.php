<?php

namespace Tests\Feature\Fixtures;

use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $table = 'comments';

    protected $guarded = [];

    public function podcast(): BelongsTo
    {
        return $this->belongsTo(Podcast::class, 'podcast_id');
    }
}
