<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\ImportRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Imports\IndikatorKinerjaUtamaSheets;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use App\Models\IKPColumn;
use App\Models\IKUYear;

class FormatIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function duplicate(): RedirectResponse
    {
        $time = IKUYear::currentTime();

        $skInsertData = [];
        $ikkInsertData = [];
        $psInsertData = [];
        $ikpInsertData = [];
        $ikpColumnInsertData = [];
        if ($time->sasaranKegiatan()->count() === 0) {
            if ($temp = IKUYear::where('year', (string) (((int) $time->year) - 1))->first()) {
                if ($temp->sasaranKegiatan()->count()) {
                    foreach ($temp->sasaranKegiatan as $sk) {
                        $currentDate = Carbon::now();

                        $skID = uuid_create();

                        $skInsertData[] = [
                            'id' => $skID,

                            ...$sk->only([
                                'number',
                                'name',
                            ]),

                            'time_id' => $time->id,

                            'created_at' => $currentDate,
                            'updated_at' => $currentDate,
                        ];
                        foreach ($sk->indikatorKinerjaKegiatan as $ikk) {
                            $ikkID = uuid_create();

                            $ikkInsertData[] = [
                                'id' => $ikkID,

                                ...$ikk->only([
                                    'number',
                                    'name',
                                ]),

                                'sasaran_kegiatan_id' => $skID,

                                'created_at' => $currentDate,
                                'updated_at' => $currentDate,
                            ];
                            foreach ($ikk->programStrategis as $ps) {
                                $psID = uuid_create();

                                $psInsertData[] = [
                                    'id' => $psID,

                                    ...$ps->only([
                                        'number',
                                        'name',
                                    ]),

                                    'indikator_kinerja_kegiatan_id' => $ikkID,

                                    'created_at' => $currentDate,
                                    'updated_at' => $currentDate,
                                ];
                                foreach ($ps->indikatorKinerjaProgram as $ikp) {
                                    $ikpID = uuid_create();

                                    $ikpInsertData[] = [
                                        'id' => $ikpID,

                                        ...$ikp->only([
                                            'definition',
                                            'number',
                                            'status',
                                            'mode',
                                            'name',
                                            'type',
                                        ]),

                                        'program_strategis_id' => $psID,

                                        'created_at' => $currentDate,
                                        'updated_at' => $currentDate,
                                    ];
                                    foreach ($ikp->columns as $column) {
                                        $ikpColumnInsertData[] = [
                                            'id' => uuid_create(),

                                            ...$column->only([
                                                'number',
                                                'file',
                                                'name',
                                            ]),

                                            'indikator_kinerja_program_id' => $ikpID,

                                            'created_at' => $currentDate,
                                            'updated_at' => $currentDate,
                                        ];
                                    }
                                }
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
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil menduplikasi format indikator kinerja utama');
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\ImportRequest $request
     * @return RedirectResponse
     */
    public function import(ImportRequest $request): RedirectResponse
    {
        Excel::import(
            new IndikatorKinerjaUtamaSheets,
            $request->file('file')
        );

        return _ControllerHelpers::Back()->with('success', 'Berhasil import format indikator kinerja utama');
    }
}
