<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IndikatorKinerjaUtamaSheets implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'DATA' => new IndikatorKinerjaUtamaImport(),
        ];
    }
}
