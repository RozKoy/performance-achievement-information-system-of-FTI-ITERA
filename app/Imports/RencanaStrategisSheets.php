<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RencanaStrategisSheets implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'DATA' => new RencanaStrategisImport(),
        ];
    }
}
