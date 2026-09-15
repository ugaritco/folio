<?php

namespace Tests\Feature\Fixtures;

use Heritage\Database\Eloquent\Relations\HasMany;
use Heritage\Foundation\Auth\User as BaseUser;

class User extends BaseUser
{
    protected $guarded = [];

    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class);
    }

    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }
}
