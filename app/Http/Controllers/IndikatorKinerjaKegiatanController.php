<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaKegiatan\EditRequest;
use App\Http\Requests\IndikatorKinerjaKegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class IndikatorKinerjaKegiatanController extends Controller
{
    /**
     * Indikator kinerja kegiatan home view
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranKegiatan $sk
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request, SasaranKegiatan $sk): Factory|View
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $data = $sk->indikatorKinerjaKegiatan()
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
            ->withCount('programStrategis AS ps')
            ->orderBy('number')
            ->get()
            ->toArray();

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.iku.ikk.home', compact([
            'data',
            'sk',
        ]));
    }

    /**
     * Indikator kinerja kegiatan add view
     * @param \App\Models\SasaranKegiatan $sk
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addView(SasaranKegiatan $sk): Factory|View
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $count = $sk->indikatorKinerjaKegiatan->count() + 1;

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

        return view('super-admin.iku.ikk.add', compact([
            'data',
            'sk',
        ]));
    }

    /**
     * Indikator kinerja kegiatan add function
     * @param \App\Http\Requests\IndikatorKinerjaKegiatan\AddRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddRequest $request, SasaranKegiatan $sk): RedirectResponse
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $number = $request['number'];
        $dataCount = $sk->indikatorKinerjaKegiatan->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $sk->indikatorKinerjaKegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $ikk = new IndikatorKinerjaKegiatan($request->safe()->all());

        $ikk->sasaranKegiatan()->associate($sk);
        $ikk->save();

        return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ikk', ['sk' => $sk->id]);
    }

    /**
     * Indikator kinerja kegiatan edit view
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editView(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): Factory|View
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
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

            if ($sk->time->year === Carbon::now()->format('Y')) {
                $previousRoute = route('super-admin-iku-ikk', [
                    'sk' => $sk->id
                ]);
            } else {
                $previousRoute = route('super-admin-achievement-iku', [
                    'year' => $sk->time->year
                ]);
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

        abort(404);
    }

    /**
     * Indikator kinerja kegiatan edit function
     * @param \App\Http\Requests\IndikatorKinerjaKegiatan\EditRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): RedirectResponse
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
            $number = (int) $request['number'];
            if ($number > $sk->indikatorKinerjaKegiatan->count()) {
                return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

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

            if ($sk->time->year === Carbon::now()->format('Y')) {
                return _ControllerHelpers::RedirectWithRoute('super-admin-iku-ikk', ['sk' => $sk->id]);
            } else {
                return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-iku', [
                    'year' => $sk->time->year
                ]);
            }
        }

        abort(404);
    }

    /**
     * Indikator kinerja kegiatan delete function
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): RedirectResponse
    {
        if ($sk->id === $ikk->sasaranKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $ikk->deleteOrTrashed();

            return back();
        }

        abort(404);
    }
}
