<?php

namespace App\Http\Controllers\SuperAdmin\Kegiatan;

use App\Http\Controllers\_ControllerHelpers;
use App\Http\Requests\Kegiatan\EditRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use App\Models\Kegiatan;

class UpdateKegiatanSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return Factory|View
     */
    public function view(SasaranStrategis $ss, Kegiatan $k): Factory|View
    {
        if ($ss->id !== $k->sasaranStrategis->id) {
            abort(404);
        }

        $count = $ss->kegiatan->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$k->number - 1] = [
            ...$data[$k->number - 1],
            'selected' => true,
        ];

        $previousRoute = $ss->time->year === Carbon::now()->format('Y') ? route('super-admin-rs-k', ['ss' => $ss->id]) : route(
            'super-admin-achievement-rs',
            [
                'year' => $ss->time->year
            ]
        );

        $ss = $ss->only([
            'number',
            'name',
            'id',
        ]);
        $k = $k->only([
            'name',
            'id',
        ]);

        return view('super-admin.rs.k.edit', compact([
            'previousRoute',
            'data',
            'ss',
            'k',
        ]));
    }

    /**
     * @param \App\Http\Requests\Kegiatan\EditRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return RedirectResponse
     */
    public function action(EditRequest $request, SasaranStrategis $ss, Kegiatan $k): RedirectResponse
    {
        if ($ss->id !== $k->sasaranStrategis->id) {
            abort(404);
        }

        $number = (int) $request['number'];
        if ($number > $ss->kegiatan->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            $currentNumber = $k->number;
            if ($number !== $currentNumber) {
                $k->number = $number;

                if ($number < $currentNumber) {
                    $ss->kegiatan()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $ss->kegiatan()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $k->name = $request['name'];
            $k->save();

            DB::commit();

            if ($ss->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-rs-k', ['ss' => $ss->id])->with('success', 'Berhasil memperbaharui kegiatan');
            }
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-rs', [
                'year' => $ss->time->year
            ])->with('success', 'Berhasil memperbaharui kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
