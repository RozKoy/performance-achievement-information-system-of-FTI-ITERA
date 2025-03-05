<?php

namespace App\Http\Controllers\SuperAdmin\ProgramStrategis;

use App\Http\Requests\ProgramStrategis\AddRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;

class CreateProgramStrategisSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return Factory|View
     */
    public function view(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): Factory|View
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id) {
            abort(404);
        }

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $count = $ikk->programStrategis->count() + 1;

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

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);
        $ikk = $ikk->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.iku.ps.add', compact([
            'data',
            'ikk',
            'sk',
        ]));
    }

    /**
     * @param \App\Http\Requests\ProgramStrategis\AddRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return RedirectResponse
     */
    public function action(AddRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id) {
            abort(404);
        }

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $number = $request['number'];
        $dataCount = $ikk->programStrategis->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            if ($number <= $dataCount) {
                $ikk->programStrategis()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $ps = new ProgramStrategis($request->safe()->all());

            $ps->indikatorKinerjaKegiatan()->associate($ikk);
            $ps->save();

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ps', ['sk' => $sk->id, 'ikk' => $ikk->id])
                ->with('success', 'Berhasil menambahkan program strategis');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
