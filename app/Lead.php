<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Lead extends Model
{
    protected $guarded = [];
    public function region(){
        return $this->belongsTo('App\Region', 'category_id');
    }
    public function turnovers(){
        return $this->belongsToMany('App\Turnover', 'lead_turnovers');
    }
    public function specialisms(){
        return $this->belongsToMany('App\Specialism', 'lead_specialisms');
    }
    public function employee_bands(){
        return $this->belongsToMany('App\EmployeeBand', 'lead_employee_bands');
    }

}
