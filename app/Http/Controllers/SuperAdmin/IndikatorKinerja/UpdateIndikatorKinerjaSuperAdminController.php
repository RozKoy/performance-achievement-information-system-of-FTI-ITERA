<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerja;

use App\Http\Requests\IndikatorKinerja\EditRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use App\Models\Kegiatan;

class UpdateIndikatorKinerjaSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @param \App\Models\IndikatorKinerja $ik
     * @return Factory|View
     */
    public function view(SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik): Factory|View
    {
        if ($ss->id !== $k->sasaranStrategis->id || $k->id !== $ik->kegiatan->id) {
            abort(404);
        }

        $count = $k->indikatorKinerja->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$ik->number - 1] = [
            ...$data[$ik->number - 1],
            'selected' => true,
        ];

        $previousRoute = route('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id]);
        $current = true;
        if ($ss->time->year !== Carbon::now()->format('Y')) {
            $previousRoute = route('super-admin-achievement-rs', ['year' => $ss->time->year]);
            $current = false;
        }

        $type = [['value' => $ik->type, 'text' => ucfirst($ik->type)]];

        $ss = $ss->only([
            'number',
            'name',
            'id',
        ]);
        $k = $k->only([
            'number',
            'name',
            'id',
        ]);
        $ik = $ik->only([
            'textSelections',
            'status',
            'name',
            'type',
            'id',
        ]);

        return view('super-admin.rs.ik.edit', compact([
            'previousRoute',
            'current',
            'data',
            'type',
            'ik',
            'ss',
            'k',
        ]));
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerja\EditRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @param \App\Models\IndikatorKinerja $ik
     * @return RedirectResponse
     */
    public function action(EditRequest $request, SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik): RedirectResponse
    {
        if ($ss->id !== $k->sasaranStrategis->id || $k->id !== $ik->kegiatan->id) {
            abort(404);
        }

        $number = (int) $request['number'];
        if ($number > $k->indikatorKinerja->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            $currentNumber = $ik->number;
            if ($number !== $currentNumber) {
                $ik->number = $number;

                if ($number < $currentNumber) {
                    $k->indikatorKinerja()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $k->indikatorKinerja()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $ik->name = $request['name'];
            $ik->save();

            DB::commit();

            if ($ss->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id])->with('success', 'Berhasil memperbaharui indikator kinerja');
            }
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-rs', [
                'year' => $ss->time->year
            ])->with('success', 'Berhasil memperbaharui indikator kinerja');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @param \App\Models\IndikatorKinerja $ik
     * @return RedirectResponse
     */
    public function statusToggle(SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik): RedirectResponse
    {
        if ($ss->id !== $k->sasaranStrategis->id && $k->id !== $ik->kegiatan->id) {
            abort(404);
        }

        $ss = SasaranStrategis::currentOrFail($k->sasaranStrategis->id);

        $ik->realization()->forceDelete();
        $ik->evaluation()->forceDelete();
        $ik->target()->forceDelete();

        $newStatus = $ik->status === 'aktif' ? 'tidak aktif' : 'aktif';

        $ik->status = $newStatus;
        $ik->save();

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbaharui status indikator kinerja');
    }
}
