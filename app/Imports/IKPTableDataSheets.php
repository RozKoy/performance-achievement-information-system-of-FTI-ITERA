<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\IndikatorKinerjaProgram;

class IKPTableDataSheets implements WithMultipleSheets
{
    public function __construct(public IndikatorKinerjaProgram $ikp, public string $period_id, public string $unit_id)
    {
    }

    public function sheets(): array
    {
        return [
            new IKPTableDataImport($this->ikp, $this->period_id, $this->unit_id),
        ];
    }
}
