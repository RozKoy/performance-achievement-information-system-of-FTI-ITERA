<?php

namespace App\Imports;

use App\Models\IndikatorKinerjaTextSelection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use App\Models\Kegiatan;
use App\Models\RSYear;

class RencanaStrategisImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows): void
    {
        $year = RSYear::currentTime();

        $ssInsertData = [];
        $kInsertData = [];
        $ikInsertData = [];
        $ikTextSelectionInsertData = [];

        $currentDate = Carbon::now();

        $ssNumber = $year->sasaranStrategis()->count() + 1;
        $kNumber = 1;
        $ikNumber = 1;

        $ssID = null;
        $kID = null;
        foreach ($rows as $key => $row) {
            if ($key !== 0) {
                if ($row[0]) {
                    $currentDate = Carbon::now();

                    $ssID = uuid_create();

                    $ssInsertData[] = [
                        'id' => $ssID,

                        'number' => $ssNumber,
                        'name' => $row[0],

                        'time_id' => $year->id,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    $ssNumber++;
                    $kNumber = 1;
                }

                if ($row[1] && $ssID) {
                    $kID = uuid_create();

                    $kInsertData[] = [
                        'id' => $kID,

                        'number' => $kNumber,
                        'name' => $row[1],

                        'sasaran_strategis_id' => $ssID,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    $kNumber++;
                    $ikNumber = 1;
                }

                if ($row[2] && $row[3] && $kID) {
                    $type = strtolower($row[3]);
                    if (in_array($type, IndikatorKinerja::getTypeValues())) {
                        $ikID = uuid_create();

                        $ikInsertData[] = [
                            'id' => $ikID,

                            'number' => $ikNumber,
                            'name' => $row[2],
                            'type' => $type,

                            'status' => 'aktif',

                            'kegiatan_id' => $kID,

                            'created_at' => $currentDate,
                            'updated_at' => $currentDate,
                        ];

                        if ($type === IndikatorKinerja::TYPE_TEXT) {
                            $options = ['ada', 'tidak ada'];
                            if ($row[4]) {
                                $options = explode('#', $row[4]);
                            }
                            if (is_array($options)) {
                                foreach ($options as $option) {
                                    $ikTextSelectionInsertData[] = [
                                        'id' => uuid_create(),

                                        'value' => $option,

                                        'indikator_kinerja_id' => $ikID,

                                        'created_at' => $currentDate,
                                        'updated_at' => $currentDate,
                                    ];
                                }
                            }
                        }

                        $ikNumber++;
                    }
                }
            }
        }

        if (count($ssInsertData)) {
            SasaranStrategis::insert($ssInsertData);
        }
        if (count($kInsertData)) {
            Kegiatan::insert($kInsertData);
        }
        if (count($ikInsertData)) {
            IndikatorKinerja::insert($ikInsertData);
        }
        if (count($ikTextSelectionInsertData)) {
            IndikatorKinerjaTextSelection::insert($ikTextSelectionInsertData);
        }
    }
}
