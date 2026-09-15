<?php

namespace Tests\Feature\Fixtures;

use Heritage\Database\Eloquent\Model;
use Heritage\Database\Eloquent\Relations\HasMany;
use Heritage\Database\Eloquent\SoftDeletes;

class Podcast extends Model
{
    use SoftDeletes;

    protected $table = 'podcasts';

    protected $guarded = [];

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
