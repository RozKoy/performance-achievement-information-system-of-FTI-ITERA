<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Collection;

class IKPTableDataImport implements ToCollection
{
    public function __construct(public IndikatorKinerjaProgram $ikp, public string $period_id, public string $unit_id)
    {
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $columns = $this->ikp->columns()
            ->where('file', false)
            ->orderBy('number')
            ->get();

        foreach ($collection as $itemKey => $item) {
            if ($itemKey > 0) {
                $temp = null;
                foreach ($columns as $columnKey => $column) {
                    $value = $item[$columnKey] ?? null;

                    if ($value !== null) {
                        $temp = $temp ?: $this->ikp->achievements()->create([
                            'period_id' => $this->period_id,
                            'unit_id' => $this->unit_id,
                        ]);

                        $temp->data()->create([
                            'column_id' => $column->id,

                            'data' => $value
                        ]);
                    }
                }
            }
        }
    }
}
