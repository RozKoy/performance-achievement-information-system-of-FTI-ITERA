<?php

namespace App\Http\Controllers\SuperAdmin\SasaranKegiatan;

use App\Http\Requests\SasaranKegiatan\EditRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;

class UpdateSasaranKegiatanSuperAdminController extends Controller
{
    /**
     * @param \App\Models\SasaranKegiatan $sk
     * @return Factory|View
     */
    public function view(SasaranKegiatan $sk): Factory|View
    {
        $count = $sk->time->sasaranKegiatan->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$sk->number - 1] = [
            ...$data[$sk->number - 1],
            'selected' => true,
        ];

        $previousRoute = $sk->time->year === Carbon::now()->format('Y') ? route('super-admin-iku-sk') : route(
            'super-admin-achievement-iku',
            [
                'year' => $sk->time->year
            ]
        );

        $sk = $sk->only([
            'name',
            'id',
        ]);

        return view('super-admin.iku.sk.edit', compact([
            'previousRoute',
            'data',
            'sk',
        ]));
    }

    /**
     * @param \App\Http\Requests\SasaranKegiatan\EditRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @return RedirectResponse
     */
    public function action(EditRequest $request, SasaranKegiatan $sk): RedirectResponse
    {
        $time = $sk->time;

        $number = (int) $request['number'];
        if ($number > $time->sasaranKegiatan->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            $currentNumber = $sk->number;
            if ($number !== $currentNumber) {
                $sk->number = $number;

                if ($number < $currentNumber) {
                    $time->sasaranKegiatan()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $time->sasaranKegiatan()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $sk->name = $request['name'];
            $sk->save();

            DB::commit();

            if ($time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-iku-sk')->with('success', 'Berhasil memperbaharui sasaran kegiatan');
            }
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-iku', [
                'year' => $time->year
            ])->with('success', 'Berhasil memperbaharui sasaran kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
