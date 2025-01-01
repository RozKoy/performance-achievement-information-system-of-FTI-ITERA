<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStrategis\EditRequest;
use App\Http\Requests\ProgramStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ProgramStrategisController extends Controller
{
    /**
     * Program strategis home view
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): Factory|View
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $data = $ikk->programStrategis()
                ->select([
                    'number',
                    'name',
                    'id',
                ])
                ->where(function (Builder $query) use ($request): void {
                    if (isset($request->search)) {
                        $query->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('number', $request->search);
                    }
                })
                ->withCount([
                    'indikatorKinerjaProgram AS active' => function (Builder $query): void {
                        $query->where('status', 'aktif');
                    },
                    'indikatorKinerjaProgram AS inactive' => function (Builder $query): void {
                        $query->where('status', 'tidak aktif');
                    }
                ])
                ->orderBy('number')
                ->get()
                ->toArray();

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

            return view('super-admin.iku.ps.home', compact([
                'data',
                'ikk',
                'sk',
            ]));
        }

        abort(404);
    }

    /**
     * Program strategis add view
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addView(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): Factory|View
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
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

        abort(404);
    }

    /**
     * Program strategis add function
     * @param \App\Http\Requests\ProgramStrategis\AddRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): RedirectResponse
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $number = $request['number'];
            $dataCount = $ikk->programStrategis->count();
            if ($number > $dataCount + 1) {
                return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

            if ($number <= $dataCount) {
                $ikk->programStrategis()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $ps = new ProgramStrategis($request->safe()->all());

            $ps->indikatorKinerjaKegiatan()->associate($ikk);
            $ps->save();

            return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ps', ['sk' => $sk->id, 'ikk' => $ikk->id]);
        }

        abort(404);
    }

    /**
     * Program strategis edit view
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editView(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): Factory|View
    {
        if ($sk->id === $ikk->sasaranKegiatan->id && $ikk->id === $ps->indikatorKinerjaKegiatan->id) {
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

            if ($sk->time->year === Carbon::now()->format('Y')) {
                $previousRoute = route('super-admin-iku-ps', [
                    'ikk' => $ikk->id,
                    'sk' => $sk->id,
                ]);
            } else {
                $previousRoute = route('super-admin-achievement-iku', [
                    'year' => $sk->time->year
                ]);
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

        abort(404);
    }

    /**
     * Program strategis edit function
     * @param \App\Http\Requests\ProgramStrategis\EditRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): RedirectResponse
    {
        if ($sk->id === $ikk->sasaranKegiatan->id && $ikk->id === $ps->indikatorKinerjaKegiatan->id) {
            $number = (int) $request['number'];
            if ($number > $ikk->programStrategis->count()) {
                return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }
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

            if ($sk->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ps', ['sk' => $sk->id, 'ikk' => $ikk->id]);
            } else {
                return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-iku', [
                    'year' => $sk->time->year
                ]);
            }
        }

        abort(404);
    }

    /**
     * Program strategis delete function
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @param \App\Models\ProgramStrategis $ps
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps): RedirectResponse
    {
        if ($sk->id === $ikk->sasaranKegiatan->id && $ikk->id === $ps->indikatorKinerjaKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $ps->deleteOrTrashed();

            return back();
        }

        abort(404);
    }
}
