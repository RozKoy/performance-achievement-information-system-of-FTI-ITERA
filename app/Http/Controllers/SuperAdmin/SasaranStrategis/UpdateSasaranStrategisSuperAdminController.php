<?php

namespace App\Http\Controllers\SuperAdmin\SasaranStrategis;

use App\Http\Requests\SasaranStrategis\EditRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;

class UpdateSasaranStrategisSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranStrategis $ss
     * @return Factory|View
     */
    public function view(SasaranStrategis $ss): Factory|View
    {
        $count = $ss->time->sasaranStrategis->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$ss->number - 1] = [
            ...$data[$ss->number - 1],
            'selected' => true,
        ];

        $previousRoute = $ss->time->year === Carbon::now()->format('Y') ? route('super-admin-rs-ss') : route(
            'super-admin-achievement-rs',
            [
                'year' => $ss->time->year
            ]
        );

        $ss = $ss->only([
            'name',
            'id',
        ]);

        return view('super-admin.rs.ss.edit', compact([
            'previousRoute',
            'data',
            'ss',
        ]));
    }

    /**
     * @param \App\Http\Requests\SasaranStrategis\EditRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @return RedirectResponse
     */
    public function action(EditRequest $request, SasaranStrategis $ss): RedirectResponse
    {
        $time = $ss->time;

        $number = (int) $request['number'];
        if ($number > $time->sasaranStrategis->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            $currentNumber = $ss->number;
            if ($number !== $currentNumber) {
                $ss->number = $number;

                if ($number < $currentNumber) {
                    $time->sasaranStrategis()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $time->sasaranStrategis()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $ss->name = $request['name'];
            $ss->save();

            DB::commit();

            if ($time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-rs-ss')->with('success', 'Berhasil memperbaharui sasaran strategis');
            } else {
                return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-rs', [
                    'year' => $time->year
                ])->with('success', 'Berhasil memperbaharui sasaran strategis');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
