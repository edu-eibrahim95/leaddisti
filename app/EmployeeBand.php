<?php

namespace App;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EmployeeBand extends Model
{
    protected $table = 'categories';
    protected $guarded = [];

    protected static function boot()
    {
        static::addGlobalScope('type', function (Builder $builder) {
            $builder->where('type', '=', 2);
        });

        parent::boot();
    }
}
