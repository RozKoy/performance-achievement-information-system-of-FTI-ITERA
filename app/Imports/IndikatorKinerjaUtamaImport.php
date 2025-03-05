<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Collection;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use App\Models\IKPColumn;
use App\Models\IKUYear;

class IndikatorKinerjaUtamaImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows): void
    {
        $year = IKUYear::currentTime();

        $skInsertData = [];
        $ikkInsertData = [];
        $psInsertData = [];
        $ikpInsertData = [];
        $ikpColumnInsertData = [];

        $currentDate = Carbon::now();

        $skNumber = $year->sasaranKegiatan()->count() + 1;
        $ikkNumber = 1;
        $psNumber = 1;
        $ikpNumber = 1;

        $skID = null;
        $ikkID = null;
        $psID = null;
        foreach ($rows as $key => $row) {
            if ($key !== 0) {
                if ($row[0]) {
                    $currentDate = Carbon::now();

                    $skID = uuid_create();

                    $skInsertData[] = [
                        'id' => $skID,

                        'number' => $skNumber,
                        'name' => $row[0],

                        'time_id' => $year->id,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    $skNumber++;
                    $ikkNumber = 1;
                }

                if ($row[1] && $skID) {
                    $ikkID = uuid_create();

                    $ikkInsertData[] = [
                        'id' => $ikkID,

                        'number' => $ikkNumber,
                        'name' => $row[1],

                        'sasaran_kegiatan_id' => $skID,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    $ikkNumber++;
                    $psNumber = 1;
                }

                if ($row[2] && $ikkID) {
                    $psID = uuid_create();

                    $psInsertData[] = [
                        'id' => $psID,

                        'number' => $psNumber,
                        'name' => $row[2],

                        'indikator_kinerja_kegiatan_id' => $ikkID,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    $psNumber++;
                    $ikpNumber = 1;
                }

                if ($row[3] && $row[4] && $psID) {
                    $type = strtolower($row[5] ?? 'iku');
                    $mode = strtolower($row[6] ?? IndikatorKinerjaProgram::MODE_TABLE);

                    if (!in_array($type, ['iku', 'ikp'])) {
                        $type = 'iku';
                    }
                    if (!in_array($mode, IndikatorKinerjaProgram::getModeValues())) {
                        $mode = IndikatorKinerjaProgram::MODE_TABLE;
                    }

                    $ikpID = uuid_create();

                    $ikpInsertData[] = [
                        'id' => $ikpID,

                        'definition' => $row[4],
                        'number' => $ikpNumber,
                        'name' => $row[3],
                        'mode' => $mode,
                        'type' => $type,

                        'status' => 'aktif',

                        'program_strategis_id' => $psID,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    if ($mode === IndikatorKinerjaProgram::MODE_TABLE) {
                        $columns = ['kolom 1', 'kolom 2'];
                        if ($row[7]) {
                            $columns = explode('#', $row[7]);
                        }
                        if (is_array($columns)) {
                            $columnNumber = 1;
                            foreach ($columns as $key => $column) {
                                $ikpColumnInsertData[] = [
                                    'id' => uuid_create(),

                                    'number' => $columnNumber,
                                    'name' => $column,

                                    'indikator_kinerja_program_id' => $ikpID,

                                    'created_at' => $currentDate,
                                    'updated_at' => $currentDate,
                                ];

                                $columnNumber++;
                            }
                        }
                    }

                    $ikpNumber++;
                }
            }
        }

        if (count($skInsertData)) {
            SasaranKegiatan::insert($skInsertData);
        }
        if (count($ikkInsertData)) {
            IndikatorKinerjaKegiatan::insert($ikkInsertData);
        }
        if (count($psInsertData)) {
            ProgramStrategis::insert($psInsertData);
        }
        if (count($ikpInsertData)) {
            IndikatorKinerjaProgram::insert($ikpInsertData);
        }
        if (count($ikpColumnInsertData)) {
            IKPColumn::insert($ikpColumnInsertData);
        }
    }
}
