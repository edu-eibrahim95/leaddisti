<?php

namespace App;

use App\Jobs\LeadEmail;
use Illuminate\Database\Eloquent\Model;


class EmailQueue extends Model
{
    protected $guarded =[];
    public static function boot()
    {
        parent::boot();
        static::created(function ($item) {
            LeadEmail::dispatch($item->id)->onQueue('leads');
        });
    }

    public function partner(){
        return $this->belongsTo('App\Partner', 'partner_id')->first();
    }
    public function lead(){
        return $this->belongsTo('App\Lead', 'lead_id')->first();
    }

}
