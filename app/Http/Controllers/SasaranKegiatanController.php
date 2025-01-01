<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranKegiatan\EditRequest;
use App\Http\Requests\SasaranKegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\IKUYear;

class SasaranKegiatanController extends Controller
{
    /**
     * Sasaran kegiatan home view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request): Factory|View
    {
        $time = IKUYear::currentTime();

        $data = $time->sasaranKegiatan()
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
            ->withCount('indikatorKinerjaKegiatan AS ikk')
            ->orderBy('number')
            ->get()
            ->toArray();

        return view('super-admin.iku.sk.home', compact('data'));
    }

    /**
     * Sasaran kegiatan add view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addView(): Factory|View
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
     * Sasaran kegiatan add function
     * @param \App\Http\Requests\SasaranKegiatan\AddRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddRequest $request): RedirectResponse
    {
        $time = IKUYear::currentTime();

        $number = (int) $request['number'];
        $dataCount = $time->sasaranKegiatan->count();

        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $time->sasaranKegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $sk = new SasaranKegiatan($request->safe()->all());

        $sk->time()->associate($time);
        $sk->save();

        return _ControllerHelpers::RedirectWithRoute('super-admin-iku-sk');
    }

    /**
     * Sasaran kegiatan edit view
     * @param \App\Models\SasaranKegiatan $sk
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editView(SasaranKegiatan $sk): Factory|View
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

        if ($sk->time->year === Carbon::now()->format('Y')) {
            $previousRoute = route('super-admin-iku-sk');
        } else {
            $previousRoute = route('super-admin-achievement-iku', [
                'year' => $sk->time->year
            ]);
        }

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
     * Sasaran kegiatan edit function
     * @param \App\Http\Requests\SasaranKegiatan\EditRequest $request
     * @param \App\Models\SasaranKegiatan $sk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, SasaranKegiatan $sk): RedirectResponse
    {
        $time = $sk->time;

        $number = (int) $request['number'];
        if ($number > $time->sasaranKegiatan->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

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

        if ($time->year === Carbon::now()->format('Y')) {
            return _ControllerHelpers::RedirectWithRoute('super-admin-iku-sk');
        } else {
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-iku', [
                'year' => $time->year
            ]);
        }
    }

    /**
     * Sasaran kegiatan delete function
     * @param \App\Models\SasaranKegiatan $sk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(SasaranKegiatan $sk): RedirectResponse
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $sk->deleteOrTrashed();

        return back();
    }
}
