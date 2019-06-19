<?php

namespace App\Imports;

use App\EmployeeBand;
use App\Partner;
use App\PartnerEmployeeBand;
use App\PartnerRegion;
use App\PartnerSpecialism;
use App\PartnerTurnover;
use App\Region;
use App\Specialism;
use App\Turnover;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class PartnerFirstSheetImport implements OnEachRow, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function onRow(Row $row)
    {
        $row      = $row->toCollection();
        $partner = Partner::create([
            'name' => $row['partner'],
            'email' => $row['email'],
            'account_manager' => $row['account_manager'],
            'account' => $row['account'],
            'sectors' => $row['sectors_covered'],
        ]);
        $regions = explode(',', $row['region']);
        foreach ($regions as $region){
            if (! trim($region)) continue;
            $region_instance = Region::where(['name'=>trim($region)])->first();
            if (! $region_instance){
                $order = Region::max('order');
                $region_instance = Region::create(['type'=> 0, 'name'=>trim($region), 'order'=>$order+1]);
            }
            PartnerRegion::create(['region_id'=>$region_instance->id, 'partner_id'=>$partner->id]);
        }
        $specialisms = explode(',', $row['specialisms']);
        foreach ($specialisms as $specialism){
            if (! trim($specialism)) continue;
            $specialism_instance = Specialism::where(['name'=>trim($specialism)])->first();
            if (! $specialism_instance){
                $order = Specialism::max('order');
                $specialism_instance = Specialism::create(['type'=> 3, 'name'=>trim($specialism), 'order'=>$order+1]);
            }
            PartnerSpecialism::create(['specialism_id'=>$specialism_instance->id, 'partner_id'=>$partner->id]);
        }
        $turnovers = explode(',', $row['turnover_of_prospects']);
        foreach ($turnovers as $turnover){
            if (! trim($turnover)) continue;
            $turnover_instance = Turnover::where(['name'=>trim($turnover)])->first();
            if (! $turnover_instance){
                $order = Turnover::max('order');
                $turnover_instance = Turnover::create(['type'=> 1, 'name'=>trim($turnover), 'order'=>$order+1]);
            }
            PartnerTurnover::create(['turnover_id'=>$turnover_instance->id, 'partner_id'=>$partner->id]);
        }
        $employee_bands = explode(',', $row['employee_no_of_prospects']);
        foreach ($employee_bands as $employee_band){
            if (! trim($employee_band)) continue;
            $employee_band_instance = EmployeeBand::where(['name'=>trim($employee_band)])->first();
            if (! $employee_band_instance){
                $order = EmployeeBand::max('order');
                $employee_band_instance = EmployeeBand::create(['type'=> 2, 'name'=>trim($employee_band), 'order'=>$order+1]);
            }
            PartnerEmployeeBand::create(['employee_band_id'=>$employee_band_instance->id, 'partner_id'=>$partner->id]);
        }
    }
}
