<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerja\EditRequest;
use App\Http\Requests\IndikatorKinerja\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class IndikatorKinerjaController extends Controller
{
    protected $type = [
        [
            'value' => 'teks',
            'text' => 'Teks',
        ],
        [
            'value' => 'angka',
            'text' => 'Angka',
        ],
        [
            'value' => 'persen',
            'text' => 'Persen',
        ],
    ];

    public function homeView(Request $request, SasaranStrategis $ss, Kegiatan $k)
    {
        if ($ss->id === $k->sasaranStrategis->id) {
            $ss = SasaranStrategis::currentOrFail($ss->id);

            $data = $k->indikatorKinerja()
                ->select([
                    'number',
                    'status',
                    'name',
                    'type',
                    'id',
                ])
                ->where(function (Builder $query) use ($request) {
                    if (isset($request->search)) {
                        $query->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('type', $request->search)
                            ->orWhere('status', $request->search)
                            ->orWhere('number', $request->search);
                    }
                })
                ->orderBy('number')
                ->get()
                ->toArray();

            $ss = $ss->only([
                'number',
                'name',
                'id',
            ]);
            $k = $k->only([
                'number',
                'name',
                'id',
            ]);

            return view('super-admin.rs.ik.home', compact([
                'data',
                'ss',
                'k',
            ]));
        }

        abort(404);
    }

    public function addView(SasaranStrategis $ss, Kegiatan $k)
    {
        if ($ss->id === $k->sasaranStrategis->id) {
            $ss = SasaranStrategis::currentOrFail($ss->id);

            $count = $k->indikatorKinerja->count() + 1;

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
            $k = $k->only([
                'number',
                'name',
                'id',
            ]);

            $type = $this->type;

            return view('super-admin.rs.ik.add', compact([
                'data',
                'type',
                'ss',
                'k'
            ]));
        }

        abort(404);
    }

    public function add(AddRequest $request, SasaranStrategis $ss, Kegiatan $k)
    {
        if ($ss->id === $k->sasaranStrategis->id) {
            $ss = SasaranStrategis::currentOrFail($ss->id);

            $number = $request['number'];
            $dataCount = $k->indikatorKinerja->count();
            if ($number > $dataCount + 1) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

            if ($number <= $dataCount) {
                $k->indikatorKinerja()
                    ->where('number', '>=', $number)
                    ->increment('number');
            }

            $ik = new IndikatorKinerja($request->safe()->all());

            $ik->kegiatan()->associate($k);
            $ik->status = 'aktif';

            $ik->save();

            return redirect()->route('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id]);
        }

        abort(404);
    }

    public function editView(SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik)
    {
        if ($ss->id === $k->sasaranStrategis->id && $k->id === $ik->kegiatan->id) {
            $count = $k->indikatorKinerja->count();

            $data = [];
            for ($i = 0; $i < $count; $i++) {
                $data[$i] = [
                    "value" => strval($i + 1),
                    "text" => strval($i + 1),
                ];
            }
            $data[$ik->number - 1] = [
                ...$data[$ik->number - 1],
                'selected' => true,
            ];

            $type = [['value' => $ik->type, 'text' => ucfirst($ik->type)]];

            $ss = $ss->only([
                'number',
                'name',
                'id',
            ]);
            $k = $k->only([
                'number',
                'name',
                'id',
            ]);
            $ik = $ik->only([
                'status',
                'name',
                'id',
            ]);

            return view('super-admin.rs.ik.edit', compact([
                'data',
                'type',
                'ik',
                'ss',
                'k',
            ]));
        }

        abort(404);
    }

    public function edit(EditRequest $request, SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik)
    {
        if ($ss->id === $k->sasaranStrategis->id && $k->id === $ik->kegiatan->id) {
            $number = (int) $request['number'];
            if ($number > $k->indikatorKinerja->count()) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

            $currentNumber = $ik->number;
            if ($number !== $currentNumber) {
                $ik->number = $number;

                if ($number < $currentNumber) {
                    $k->indikatorKinerja()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $k->indikatorKinerja()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $ik->name = $request['name'];
            $ik->save();

            if ($ss->time->year === Carbon::now()->format('Y')) {
                return redirect()->route('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id]);
            } else {
                return redirect()->route('super-admin-achievement-rs', [
                    'year' => $ss->time->year
                ]);
            }
        }

        abort(404);
    }

    public function statusToggle(SasaranStrategis $ss, Kegiatan $k, IndikatorKinerja $ik)
    {
        if ($ss->id === $k->sasaranStrategis->id && $k->id === $ik->kegiatan->id) {
            $ik->realization()->forceDelete();
            $ik->evaluation()->forceDelete();
            $ik->target()->forceDelete();

            $newStatus = $ik->status === 'aktif' ? 'tidak aktif' : 'aktif';

            $ik->status = $newStatus;
            $ik->save();

            return back();
        }

        abort(404);
    }

    public function delete($id)
    {
        $ik = IndikatorKinerja::findOrFail($id);
        $k = Kegiatan::findOrFail($ik->kegiatan->id);
        $ss = SasaranStrategis::currentOrFail($k->sasaranStrategis->id);

        $ik->deleteOrTrashed();

        return back();
    }
}
