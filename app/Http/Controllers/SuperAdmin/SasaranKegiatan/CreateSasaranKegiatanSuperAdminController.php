<?php

namespace App\Http\Controllers\SuperAdmin\SasaranKegiatan;

use App\Http\Requests\SasaranKegiatan\AddRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\SasaranKegiatan;
use App\Models\IKUYear;

class CreateSasaranKegiatanSuperAdminController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        $time = IKUYear::currentTime();

        $count = $time->sasaranKegiatan->count() + 1;

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

        return view('super-admin.iku.sk.add', compact('data'));
    }

    /**
     * @param \App\Http\Requests\SasaranKegiatan\AddRequest $request
     * @return RedirectResponse
     */
    public function action(AddRequest $request): RedirectResponse
    {
        $time = IKUYear::currentTime();

        $number = (int) $request['number'];
        $dataCount = $time->sasaranKegiatan->count();

        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            if ($number <= $dataCount) {
                $time->sasaranKegiatan()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $sk = new SasaranKegiatan($request->safe()->all());

            $sk->time()->associate($time);
            $sk->save();

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('super-admin-iku-sk')->with('success', 'Berhasil menambahkan sasaran kegiatan');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
