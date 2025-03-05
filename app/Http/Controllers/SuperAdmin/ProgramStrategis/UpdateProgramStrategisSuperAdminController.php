<?php

namespace App\Http\Controllers\SuperAdmin\ProgramStrategis;

use App\Http\Requests\ProgramStrategis\EditRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;

class UpdateProgramStrategisSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return Factory|View
     */
    public function view(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): Factory|View
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id || $ikk->id !== $ps->indikatorKinerjaKegiatan->id) {
            abort(404);
        }

        $count = $ikk->programStrategis->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$ps->number - 1] = [
            ...$data[$ps->number - 1],
            'selected' => true,
        ];

        $previousRoute = route('super-admin-iku-ps', ['ikk' => $ikk->id, 'sk' => $sk->id]);
        if ($sk->time->year !== Carbon::now()->format('Y')) {
            $previousRoute = route('super-admin-achievement-iku', ['year' => $sk->time->year]);
        }

        $ikk = $ikk->only([
            'number',
            'name',
            'id',
        ]);
        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);
        $ps = $ps->only([
            'name',
            'id',
        ]);

        return view('super-admin.iku.ps.edit', compact([
            'previousRoute',
            'data',
            'ikk',
            'ps',
            'sk',
        ]));
    }

    /**
     * @param \App\Http\Requests\ProgramStrategis\EditRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return RedirectResponse
     */
    public function action(EditRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id || $ikk->id !== $ps->indikatorKinerjaKegiatan->id) {
            abort(404);
        }

        $number = (int) $request['number'];
        if ($number > $ikk->programStrategis->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            $currentNumber = $ps->number;
            if ($number !== $currentNumber) {
                $ps->number = $number;

                if ($number < $currentNumber) {
                    $ikk->programStrategis()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $ikk->programStrategis()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $ps->name = $request['name'];
            $ps->save();

            DB::commit();

            if ($sk->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ps', ['sk' => $sk->id, 'ikk' => $ikk->id])
                    ->with('success', 'Berhasil memperbaharui program strategis');
            }
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-iku', [
                'year' => $sk->time->year
            ])->with('success', 'Berhasil memperbaharui program strategis');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
