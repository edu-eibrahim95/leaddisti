<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Turnover extends Model
{
    protected $table = 'categories';
    protected $guarded = [];

    protected static function boot()
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', '=', 1);
        });

        parent::boot();
    }
}
