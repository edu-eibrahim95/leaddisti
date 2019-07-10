<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Partner extends Model
{
    protected $guarded = [];
    protected $perPage = 100;
    public function regions(){
        return $this->belongsToMany('App\Region', 'partner_regions');
    }
    public function turnovers(){
        return $this->belongsToMany('App\Turnover', 'partner_turnovers');
    }
    public function specialisms(){
        return $this->belongsToMany('App\Specialism', 'partner_specialisms');
    }
    public function employee_bands(){
        return $this->belongsToMany('App\EmployeeBand', 'partner_employee_bands');
    }
}
