<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranStrategis\EditRequest;
use App\Http\Requests\SasaranStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSYear;

class SasaranStrategisController extends Controller
{
    public function homeView(Request $request)
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

    public function addView()
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

    public function add(AddRequest $request)
    {
        $time = RSYear::currentTime();

        $number = (int) $request['number'];
        $dataCount = $time->sasaranStrategis->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $time->sasaranStrategis()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $ss = new SasaranStrategis($request->safe()->all());

        $ss->time()->associate($time);
        $ss->save();

        return redirect()->route('super-admin-rs-ss');
    }

    public function editView(SasaranStrategis $ss)
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

    public function edit(EditRequest $request, SasaranStrategis $ss)
    {
        $time = $ss->time;

        $number = (int) $request['number'];
        if ($number > $time->sasaranStrategis->count()) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
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
            return redirect()->route('super-admin-rs-ss');
        } else {
            return redirect()->route('super-admin-achievement-rs', [
                'year' => $time->year
            ]);
        }
    }

    public function delete($id)
    {
        $ss = SasaranStrategis::currentOrFail($id);

        $ss->deleteOrTrashed();

        return back();
    }
}
