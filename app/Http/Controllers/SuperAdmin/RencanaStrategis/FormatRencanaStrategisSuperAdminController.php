<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use App\Http\Requests\RencanaStrategis\ImportRequest;
use App\Models\IndikatorKinerjaTextSelection;
use App\Http\Controllers\_ControllerHelpers;
use App\Imports\RencanaStrategisSheets;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use App\Models\Kegiatan;
use App\Models\RSYear;

class FormatRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function duplicate(): RedirectResponse
    {
        $time = RSYear::currentTime();

        $ssInsertData = [];
        $kInsertData = [];
        $ikInsertData = [];
        $ikTextSelectionInsertData = [];
        if ($time->sasaranStrategis()->count() === 0) {
            if ($temp = RSYear::where('year', (string) (((int) $time->year) - 1))->first()) {
                if ($temp->sasaranStrategis()->count()) {
                    foreach ($temp->sasaranStrategis as $ss) {
                        $currentDate = Carbon::now();

                        $ssID = uuid_create();

                        $ssInsertData[] = [
                            'id' => $ssID,

                            ...$ss->only([
                                'number',
                                'name',
                            ]),

                            'time_id' => $time->id,

                            'created_at' => $currentDate,
                            'updated_at' => $currentDate,
                        ];
                        foreach ($ss->kegiatan as $k) {
                            $kID = uuid_create();

                            $kInsertData[] = [
                                'id' => $kID,

                                ...$k->only([
                                    'number',
                                    'name',
                                ]),

                                'sasaran_strategis_id' => $ssID,

                                'created_at' => $currentDate,
                                'updated_at' => $currentDate,
                            ];
                            foreach ($k->indikatorKinerja as $ik) {
                                $ikID = uuid_create();

                                $ikInsertData[] = [
                                    'id' => $ikID,

                                    ...$ik->only([
                                        'number',
                                        'status',
                                        'name',
                                        'type',
                                    ]),

                                    'kegiatan_id' => $kID,

                                    'created_at' => $currentDate,
                                    'updated_at' => $currentDate,
                                ];
                                foreach ($ik->textSelections as $textSelection) {
                                    $ikTextSelectionInsertData[] = [
                                        'id' => uuid_create(),

                                        ...$textSelection->only([
                                            'value',
                                        ]),

                                        'indikator_kinerja_id' => $ikID,

                                        'created_at' => $currentDate,
                                        'updated_at' => $currentDate,
                                    ];
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
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil menduplikasi format rencana strategis');
    }

    /**
     * @param \App\Http\Requests\RencanaStrategis\ImportRequest $request
     * @return RedirectResponse
     */
    public function import(ImportRequest $request): RedirectResponse
    {
        Excel::import(
            new RencanaStrategisSheets,
            $request->file('file')
        );

        return _ControllerHelpers::Back()->with('success', 'Berhasil import format rencana strategis');
    }
}
