<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranStrategis\EditRequest;
use App\Http\Requests\SasaranStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSYear;

class SasaranStrategisController extends Controller
{
    /**
     * Sasaran strategis home view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request): Factory|View
    {
        $time = RSYear::currentTime();

        $data = $time->sasaranStrategis()
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
            ->withCount('kegiatan AS k')
            ->orderBy('number')
            ->get()
            ->toArray();

        return view('super-admin.rs.ss.home', compact('data'));
    }

    /**
     * Sasaran strategis add view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addView(): Factory|View
    {
        $time = RSYear::currentTime();

        $count = $time->sasaranStrategis->count() + 1;

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

        return view('super-admin.rs.ss.add', compact('data'));
    }

    /**
     * Sasaran strategis add function
     * @param \App\Http\Requests\SasaranStrategis\AddRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddRequest $request): RedirectResponse
    {
        $time = RSYear::currentTime();

        $number = (int) $request['number'];
        $dataCount = $time->sasaranStrategis->count();
        if ($number > $dataCount + 1) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $time->sasaranStrategis()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $ss = new SasaranStrategis($request->safe()->all());

        $ss->time()->associate($time);
        $ss->save();

        return _ControllerHelpers::RedirectWithRoute('super-admin-rs-ss');
    }

    /**
     * Sasaran strategis edit view
     * @param \App\Models\SasaranStrategis $ss
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editView(SasaranStrategis $ss): Factory|View
    {
        $count = $ss->time->sasaranStrategis->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$ss->number - 1] = [
            ...$data[$ss->number - 1],
            'selected' => true,
        ];

        if ($ss->time->year === Carbon::now()->format('Y')) {
            $previousRoute = route('super-admin-rs-ss');
        } else {
            $previousRoute = route('super-admin-achievement-rs', [
                'year' => $ss->time->year
            ]);
        }

        $ss = $ss->only([
            'name',
            'id',
        ]);

        return view('super-admin.rs.ss.edit', compact([
            'previousRoute',
            'data',
            'ss',
        ]));
    }

    /**
     * Sasaran strategis edit function
     * @param \App\Http\Requests\SasaranStrategis\EditRequest $request
     * @param \App\Models\SasaranStrategis $ss
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, SasaranStrategis $ss): RedirectResponse
    {
        $time = $ss->time;

        $number = (int) $request['number'];
        if ($number > $time->sasaranStrategis->count()) {
            return _ControllerHelpers::BackWithInputWithErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        $currentNumber = $ss->number;
        if ($number !== $currentNumber) {
            $ss->number = $number;

            if ($number < $currentNumber) {
                $time->sasaranStrategis()
                    ->where('number', '>=', $number)
                    ->where('number', '<', $currentNumber)
                    ->increment('number');
            } else {
                $time->sasaranStrategis()
                    ->where('number', '<=', $number)
                    ->where('number', '>', $currentNumber)
                    ->decrement('number');
            }
        }

        $ss->name = $request['name'];
        $ss->save();

        if ($time->year === Carbon::now()->format('Y')) {
            return _ControllerHelpers::RedirectWithRoute('super-admin-rs-ss');
        } else {
            return _ControllerHelpers::RedirectWithRoute('super-admin-achievement-rs', [
                'year' => $time->year
            ]);
        }
    }

    /**
     * Sasaran strategis delete function
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id): RedirectResponse
    {
        $ss = SasaranStrategis::currentOrFail($id);

        $ss->deleteOrTrashed();

        return back();
    }
}
