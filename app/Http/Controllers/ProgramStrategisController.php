<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStrategis\EditRequest;
use App\Http\Requests\ProgramStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class ProgramStrategisController extends Controller
{
    public function homeView(Request $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk)
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $data = $ikk->programStrategis()
                ->select([
                    'number',
                    'name',
                    'id',
                ])
                ->where(function (Builder $query) use ($request) {
                    if (isset($request->search)) {
                        $query->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('number', $request->search);
                    }
                })
                ->withCount([
                    'indikatorKinerjaProgram AS active' => function (Builder $query) {
                        $query->where('status', 'aktif');
                    },
                    'indikatorKinerjaProgram AS inactive' => function (Builder $query) {
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

    public function addView(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk)
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

    public function add(AddRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk)
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $number = $request['number'];
            $dataCount = $ikk->programStrategis->count();
            if ($number > $dataCount + 1) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

            if ($number <= $dataCount) {
                $ikk->programStrategis()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $ps = new ProgramStrategis($request->safe()->all());

            $ps->indikatorKinerjaKegiatan()->associate($ikk);
            $ps->save();

            return redirect()->route('super-admin-iku-ps', ['sk' => $sk->id, 'ikk' => $ikk->id]);
        }

        abort(404);
    }

    public function editView(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps)
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
                'data',
                'ikk',
                'ps',
                'sk',
            ]));
        }

        abort(404);
    }

    public function edit(EditRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps)
    {
        if ($sk->id === $ikk->sasaranKegiatan->id && $ikk->id === $ps->indikatorKinerjaKegiatan->id) {
            $number = (int) $request['number'];
            if ($number > $ikk->programStrategis->count()) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
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
                return redirect()->route('super-admin-iku-ps', ['sk' => $sk->id, 'ikk' => $ikk->id]);
            } else {
                return redirect()->route('super-admin-achievement-iku', [
                    'year' => $sk->time->year
                ]);
            }
        }

        abort(404);
    }
}
