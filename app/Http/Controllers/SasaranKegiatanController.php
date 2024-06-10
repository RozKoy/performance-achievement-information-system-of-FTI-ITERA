<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranKegiatan\EditRequest;
use App\Http\Requests\SasaranKegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\IKUYear;

class SasaranKegiatanController extends Controller
{
    public function homeView(Request $request)
    {
        $time = IKUYear::currentTime();

        $data = $time->sasaranKegiatan()
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
            ->withCount('indikatorKinerjaKegiatan AS ikk')
            ->orderBy('number')
            ->get()
            ->toArray();

        return view('super-admin.iku.sk.home', compact('data'));
    }

    public function addView()
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

    public function add(AddRequest $request)
    {
        $time = IKUYear::currentTime();

        $number = (int) $request['number'];
        $dataCount = $time->sasaranKegiatan->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $time->sasaranKegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $sk = new SasaranKegiatan($request->safe()->all());

        $sk->time()->associate($time);
        $sk->save();

        return redirect()->route('super-admin-iku-sk');
    }

    public function editView(SasaranKegiatan $sk)
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

        $sk = $sk->only([
            'name',
            'id',
        ]);

        return view('super-admin.iku.sk.edit', compact([
            'data',
            'sk',
        ]));
    }

    public function edit(EditRequest $request, SasaranKegiatan $sk)
    {
        $time = $sk->time;

        $number = (int) $request['number'];
        if ($number > $time->sasaranKegiatan->count()) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
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
            return redirect()->route('super-admin-iku-sk');
        } else {
            return redirect()->route('super-admin-achievement-iku', [
                'year' => $time->year
            ]);
        }
    }

    public function delete(SasaranKegiatan $sk)
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $sk->deleteOrTrashed();

        return back();
    }
}
