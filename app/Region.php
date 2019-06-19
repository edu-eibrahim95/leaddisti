<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;

class Region extends Category
{
    protected $table = 'categories';
    protected $guarded = [];

    protected static function boot()
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', '=', 0);
        });

        parent::boot();
    }
}
