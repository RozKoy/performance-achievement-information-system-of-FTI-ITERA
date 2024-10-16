<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kegiatan\EditRequest;
use App\Http\Requests\Kegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    /**
     * Kegiatan home view
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranStrategis $ss
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request, SasaranStrategis $ss): Factory|View
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
                    $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('number', $request->search);
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

    /**
     * Kegiatan add view
     * @param \App\Models\SasaranStrategis $ss
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addView(SasaranStrategis $ss): Factory|View
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
     * Kegiatan add function
     * @param \App\Http\Requests\Kegiatan\AddRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddRequest $request, SasaranStrategis $ss): RedirectResponse
    {
        $ss = SasaranStrategis::currentOrFail($ss->id);

        $number = $request['number'];
        $dataCount = $ss->kegiatan->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $ss->kegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $k = new Kegiatan($request->safe()->all());

        $k->sasaranStrategis()->associate($ss);
        $k->save();

        return _ControllerHelpers::RedirectWithRoute('super-admin-rs-k', ['ss' => $ss->id]);
    }

    /**
     * Kegiatan edit view
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editView(SasaranStrategis $ss, Kegiatan $k): Factory|View
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

            if ($ss->time->year === Carbon::now()->format('Y')) {
                $previousRoute = route('super-admin-rs-k', ['ss' => $ss->id]);
            } else {
                $previousRoute = route('super-admin-achievement-rs', [
                    'year' => $ss->time->year
                ]);
            }

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
                'previousRoute',
                'data',
                'ss',
                'k',
            ]));
        }

        abort(404);
    }

    /**
     * Kegiatan edit function
     * @param \App\Http\Requests\Kegiatan\EditRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, SasaranStrategis $ss, Kegiatan $k): RedirectResponse
    {
        if ($ss->id === $k->sasaranStrategis->id) {
            $number = (int) $request['number'];
            if ($number > $ss->kegiatan->count()) {
                return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
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

            $k->name = $request['name'];
            $k->save();

            if ($ss->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-rs-k', ['ss' => $ss->id]);
            } else {
                return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-rs', [
                    'year' => $ss->time->year
                ]);
            }
        }

        abort(404);
    }

    /**
     * Kegiatan delete function
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(SasaranStrategis $ss, Kegiatan $k): RedirectResponse
    {
        if ($ss->id === $k->sasaranStrategis->id) {
            $ss = SasaranStrategis::currentOrFail($ss->id);

            $k->deleteOrTrashed();

            return back();
        }

        abort(404);
    }
}
