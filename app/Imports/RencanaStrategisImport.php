<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use App\Models\RSYear;

class RencanaStrategisImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $year = RSYear::currentTime();

        $ss = null;
        $k = null;
        foreach ($rows as $key => $row) {
            if ($key !== 0) {
                if ($row[0]) {
                    $ss = $year->sasaranStrategis()->create([
                        'number' => $year->sasaranStrategis()->count() + 1,
                        'name' => $row[0],
                    ]);
                }

                if ($row[1] && $ss) {
                    $k = $ss->kegiatan()->create([
                        'number' => $ss->kegiatan()->count() + 1,
                        'name' => $row[1],
                    ]);
                }

                if ($row[2] && $row[3] && $k) {
                    $type = strtolower($row[3]);
                    if (in_array($type, ['teks', 'angka', 'persen'])) {
                        $ik = $k->indikatorKinerja()->create([
                            'number' => $k->indikatorKinerja()->count() + 1,
                            'name' => $row[2],
                            'type' => $type,

                            'status' => 'aktif',
                        ]);

                        if ($type === 'teks') {
                            $options = ['ada', 'tidak ada'];
                            if ($row[4]) {
                                $options = explode('#', $row[4]);
                            }
                            if (is_array($options)) {
                                foreach ($options as $option) {
                                    $ik->textSelections()->create([
                                        'value' => $option,
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
