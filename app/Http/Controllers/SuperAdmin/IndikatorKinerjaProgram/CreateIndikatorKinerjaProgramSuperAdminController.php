<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaProgram;

use App\Http\Requests\IndikatorKinerjaProgram\AddRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;

class CreateIndikatorKinerjaProgramSuperAdminController extends Controller
{
    protected $types = HomeIndikatorKinerjaProgramSuperAdminController::TYPES;

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

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $count = $ps->indikatorKinerjaProgram->count() + 1;

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

        $types = $this->types;
        $types[0] = [
            ...$types[0],
            'selected' => true
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
        $ps = $ps->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.iku.ikp.add', compact([
            'types',
            'data',
            'ikk',
            'ps',
            'sk',
        ]));
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaProgram\AddRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return RedirectResponse
     */
    public function action(AddRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): RedirectResponse
    {
        if ($sk->id !== $ikk->sasaranKegiatan->id || $ikk->id !== $ps->indikatorKinerjaKegiatan->id) {
            abort(404);
        }

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $number = $request['number'];
        $dataCount = $ps->indikatorKinerjaProgram->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        DB::beginTransaction();

        try {
            if ($number <= $dataCount) {
                $ps->indikatorKinerjaProgram()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $ikp = new IndikatorKinerjaProgram($request->safe()->except('columns', 'file', 'mode'));

            $ikp->programStrategis()->associate($ps);
            $ikp->mode = $request['mode'] ?? IndikatorKinerjaProgram::MODE_TABLE;
            $ikp->status = 'aktif';

            $ikp->save();

            if ($ikp->mode === IndikatorKinerjaProgram::MODE_TABLE) {
                $index = 1;
                foreach ($request['columns'] as $value) {
                    $ikp->columns()->create([
                        'number' => $index,
                        'name' => $value
                    ]);

                    $index++;
                }

                if ($request['file'] !== null) {
                    $ikp->columns()->create([
                        'name' => $request['file'],
                        'number' => $index,
                        'file' => true
                    ]);
                }
            }

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ikp', ['sk' => $sk->id, 'ikk' => $ikk->id, 'ps' => $ps->id])
                ->with('success', 'Berhasil menambahkan indikator kinerja program');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
