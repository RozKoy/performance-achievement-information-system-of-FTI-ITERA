<?php

namespace App\Http\Controllers\SuperAdmin\SasaranStrategis;

use App\Http\Requests\SasaranStrategis\AddRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranStrategis;
use App\Models\RSYear;

class CreateSasaranStrategisSuperAdminController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        $time = RSYear::currentTime();

        $count = $time->sasaranStrategis->count() + 1;

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$count - 1] = [
            ...$data[$count - 1],
            'selected' => true,
        ];

        return view('super-admin.rs.ss.add', compact('data'));
    }

    /**
     * @param \App\Http\Requests\SasaranStrategis\AddRequest $request
     * @return RedirectResponse
     */
    public function action(AddRequest $request): RedirectResponse
    {
        $time = RSYear::currentTime();

        $number = (int) $request['number'];
        $dataCount = $time->sasaranStrategis->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            if ($number <= $dataCount) {
                $time->sasaranStrategis()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $ss = new SasaranStrategis($request->safe()->all());

            $ss->time()->associate($time);
            $ss->save();

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('super-admin-rs-ss');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
