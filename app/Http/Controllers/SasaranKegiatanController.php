<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranKegiatan\EditRequest;
use App\Http\Requests\SasaranKegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;
use App\Models\IKUTime;

class SasaranKegiatanController extends Controller
{
    public function homeView(Request $request)
    {
        $time = IKUTime::currentTime();

        $data = $time->sasaranKegiatan()->select(['id', 'name', 'number'])
            ->where(function (Builder $query) use ($request) {
                if (isset ($request->search)) {
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
        $time = IKUTime::currentTime();

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
        $time = IKUTime::currentTime();

        $number = (int) $request->safe()['number'];
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

        $sasaranKegiatan = new SasaranKegiatan($request->safe()->all());

        $sasaranKegiatan->deadline()->associate($time);
        $sasaranKegiatan->time()->associate($time);

        $sasaranKegiatan->save();

        return redirect()->route('super-admin-iku-sk');
    }

    public function editView($id)
    {
        $sk = SasaranKegiatan::findOrFail($id);

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

        $sk = $sk->only(['id', 'name']);

        return view('super-admin.iku.sk.edit', compact(['data', 'sk']));
    }

    public function edit(EditRequest $request, $id)
    {
        $sk = SasaranKegiatan::findOrFail($id);
        $time = $sk->time;

        $number = (int) $request->safe()['number'];
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

        $sk->name = $request->safe()['name'];
        $sk->save();

        return redirect()->route('super-admin-iku-sk');
    }
}
