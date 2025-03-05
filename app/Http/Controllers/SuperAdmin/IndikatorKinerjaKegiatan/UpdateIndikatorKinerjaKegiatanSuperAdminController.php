<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan;

use App\Http\Requests\IndikatorKinerjaKegiatan\EditRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;

class UpdateIndikatorKinerjaKegiatanSuperAdminController extends Controller
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

        $count = $sk->indikatorKinerjaKegiatan->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$ikk->number - 1] = [
            ...$data[$ikk->number - 1],
            'selected' => true,
        ];

        $previousRoute = route('super-admin-iku-ikk', ['sk' => $sk->id]);
        if ($sk->time->year !== Carbon::now()->format('Y')) {
            $previousRoute = route('super-admin-achievement-iku', ['year' => $sk->time->year]);
        }

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);
        $ikk = $ikk->only([
            'name',
            'id',
        ]);

        return view('super-admin.iku.ikk.edit', compact([
            'previousRoute',
            'data',
            'ikk',
            'sk',
        ]));
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaKegiatan\EditRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return RedirectResponse
     */
    public function action(EditRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id) {
            abort(404);
        }

        $number = (int) $request['number'];
        if ($number > $sk->indikatorKinerjaKegiatan->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            $currentNumber = $ikk->number;
            if ($number !== $currentNumber) {
                $ikk->number = $number;

                if ($number < $currentNumber) {
                    $sk->indikatorKinerjaKegiatan()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $sk->indikatorKinerjaKegiatan()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $ikk->name = $request['name'];
            $ikk->save();

            DB::commit();

            if ($sk->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ikk', ['sk' => $sk->id])->with('success', 'Berhasil memperbaharui indikator kinerja kegiatan');
            }
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-iku', [
                'year' => $sk->time->year
            ])->with('success', 'Berhasil memperbaharui indikator kinerja kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
