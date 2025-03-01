<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Collection;
use App\Models\IKUYear;

class IndikatorKinerjaUtamaImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $year = IKUYear::currentTime();

        $sk = null;
        $ikk = null;
        $ps = null;
        foreach ($rows as $key => $row) {
            if ($key !== 0) {
                if ($row[0]) {
                    $sk = $year->sasaranKegiatan()->create([
                        'number' => $year->sasaranKegiatan()->count() + 1,
                        'name' => $row[0],
                    ]);
                }

                if ($row[1] && $sk) {
                    $ikk = $sk->indikatorKinerjaKegiatan()->create([
                        'number' => $sk->indikatorKinerjaKegiatan()->count() + 1,
                        'name' => $row[1],
                    ]);
                }

                if ($row[2] && $ikk) {
                    $ps = $ikk->programStrategis()->create([
                        'number' => $ikk->programStrategis()->count() + 1,
                        'name' => $row[2],
                    ]);
                }

                if ($row[3] && $row[4] && $ps) {
                    $type = strtolower($row[5] ?? 'iku');
                    $mode = strtolower($row[6] ?? IndikatorKinerjaProgram::MODE_TABLE);

                    if (!in_array($type, ['iku', 'ikp'])) {
                        $type = 'iku';
                    }
                    if (!in_array($mode, IndikatorKinerjaProgram::getModeValues())) {
                        $mode = IndikatorKinerjaProgram::MODE_TABLE;
                    }

                    $ikp = $ps->indikatorKinerjaProgram()->create([
                        'number' => $ps->indikatorKinerjaProgram()->count() + 1,
                        'definition' => $row[4],
                        'name' => $row[3],
                        'mode' => $mode,
                        'type' => $type,

                        'status' => 'aktif',
                    ]);

                    if ($mode === IndikatorKinerjaProgram::MODE_TABLE) {
                        $columns = ['kolom 1', 'kolom 2'];
                        if ($row[7]) {
                            $columns = explode('#', $row[7]);
                        }
                        if (is_array($columns)) {
                            foreach ($columns as $key => $column) {
                                $ikp->columns()->create([
                                    'number' => $key + 1,
                                    'name' => $column,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
