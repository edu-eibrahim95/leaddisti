<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PartnerImport implements WithMultipleSheets
{
    /**
     * @return array
     */
    public function sheets(): array
    {
        return [
            // Select by sheet index
            0 => new PartnerFirstSheetImport(),

        ];
    }
}
