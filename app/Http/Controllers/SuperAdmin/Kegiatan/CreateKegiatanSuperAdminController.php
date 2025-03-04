<?php

namespace App\Http\Controllers\SuperAdmin\Kegiatan;

use App\Http\Controllers\_ControllerHelpers;
use App\Http\Requests\Kegiatan\AddRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranStrategis;
use App\Models\Kegiatan;

class CreateKegiatanSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranStrategis $ss
     * @return Factory|View
     */
    public function view(SasaranStrategis $ss): Factory|View
    {
        $ss = SasaranStrategis::currentOrFail($ss->id);

        $count = $ss->kegiatan->count() + 1;

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

        $ss = $ss->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.rs.k.add', compact([
            'data',
            'ss',
        ]));
    }

    /**
     * @param \App\Http\Requests\Kegiatan\AddRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @return RedirectResponse
     */
    public function action(AddRequest $request, SasaranStrategis $ss): RedirectResponse
    {
        $ss = SasaranStrategis::currentOrFail($ss->id);

        $number = $request['number'];
        $dataCount = $ss->kegiatan->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            if ($number <= $dataCount) {
                $ss->kegiatan()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $k = new Kegiatan($request->safe()->all());

            $k->sasaranStrategis()->associate($ss);
            $k->save();

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('super-admin-rs-k', ['ss' => $ss->id])->with('success', 'Berhasil menambahkan kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
