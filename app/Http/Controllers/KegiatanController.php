<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kegiatan\EditRequest;
use App\Http\Requests\Kegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SasaranStrategis;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    public function homeView(Request $request, SasaranStrategis $ss)
    {
        $ss = SasaranStrategis::currentOrFail($ss->id);

        $data = $ss->kegiatan()
            ->select([
                'number',
                'name',
                'id',
            ])
            ->where(function (Builder $query) use ($request) {
                if (isset($request->search)) {
                    $query->whereAny(
                        [
                            'number',
                            'name',
                        ],
                        'LIKE',
                        "%{$request->search}%"
                    );
                }
            })
            ->withCount([
                'indikatorKinerja AS active' => function (Builder $query) {
                    $query->where('status', 'aktif');
                },
                'indikatorKinerja AS inactive' => function (Builder $query) {
                    $query->where('status', 'tidak aktif');
                }
            ])
            ->orderBy('number')
            ->get()
            ->toArray();

        $ss = $ss->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.rs.k.home', compact([
            'data',
            'ss',
        ]));
    }

    public function addView(SasaranStrategis $ss)
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

    public function add(AddRequest $request, SasaranStrategis $ss)
    {
        $ss = SasaranStrategis::currentOrFail($ss->id);

        $number = $request['number'];
        $dataCount = $ss->kegiatan->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $ss->kegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $k = new Kegiatan($request->safe()->all());

        $k->sasaranStrategis()->associate($ss);
        $k->save();

        return redirect()->route('super-admin-rs-k', ['ss' => $ss->id]);
    }

    public function editView(SasaranStrategis $ss, Kegiatan $k)
    {
        if ($ss->id === $k->sasaranStrategis->id) {
            $count = $ss->kegiatan->count();

            $data = [];
            for ($i = 0; $i < $count; $i++) {
                $data[$i] = [
                    "value" => strval($i + 1),
                    "text" => strval($i + 1),
                ];
            }
            $data[$k->number - 1] = [
                ...$data[$k->number - 1],
                'selected' => true,
            ];

            $ss = $ss->only([
                'number',
                'name',
                'id',
            ]);
            $k = $k->only([
                'name',
                'id',
            ]);

            return view('super-admin.rs.k.edit', compact([
                'data',
                'ss',
                'k',
            ]));
        }

        abort(404);
    }

    public function edit(EditRequest $request, $ssId, $id)
    {
        $k = Kegiatan::findOrFail($id);
        $ss = $k->sasaranStrategis;

        if ($ss->id === $ssId) {
            $number = (int) $request->safe()['number'];
            if ($number > $ss->kegiatan->count()) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

            $currentNumber = $k->number;
            if ($number !== $currentNumber) {
                $k->number = $number;

                if ($number < $currentNumber) {
                    $ss->kegiatan()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $ss->kegiatan()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $k->name = $request->safe()['name'];
            $k->save();

            return redirect()->route('super-admin-rs-k', ['ss' => $ssId]);
        }

        abort(404);
    }

    public function delete($id)
    {
        $k = Kegiatan::findOrFail($id);
        $ss = SasaranStrategis::currentOrFail($k->sasaranStrategis->id);

        $k->deleteOrTrashed();

        return back();
    }
}
