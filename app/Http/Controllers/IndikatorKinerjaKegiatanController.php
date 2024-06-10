<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaKegiatan\EditRequest;
use App\Http\Requests\IndikatorKinerjaKegiatan\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class IndikatorKinerjaKegiatanController extends Controller
{
    public function homeView(Request $request, SasaranKegiatan $sk)
    {
        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $data = $sk->indikatorKinerjaKegiatan()
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

    public function addView(SasaranKegiatan $sk)
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

    public function add(AddRequest $request, $skId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);

        $number = $request->safe()['number'];
        $dataCount = $sk->indikatorKinerjaKegiatan->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $sk->indikatorKinerjaKegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $ikk = new IndikatorKinerjaKegiatan($request->safe()->all());

        $ikk->sasaranKegiatan()->associate($sk);

        $ikk->save();

        return redirect()->route('super-admin-iku-ikk', ['sk' => $skId]);
    }

    public function editView($skId, $id)
    {
        $ikk = IndikatorKinerjaKegiatan::findOrFail($id);
        $sk = $ikk->sasaranKegiatan;

        if ($sk->id === $skId) {
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

            $sk = $sk->only(['id', 'name', 'number']);
            $ikk = $ikk->only(['id', 'name']);

            return view('super-admin.iku.ikk.edit', compact(['data', 'ikk', 'sk']));
        }

        abort(404);
    }

    public function edit(EditRequest $request, $skId, $id)
    {
        $ikk = IndikatorKinerjaKegiatan::findOrFail($id);
        $sk = $ikk->sasaranKegiatan;

        if ($sk->id === $skId) {
            $number = (int) $request->safe()['number'];
            if ($number > $sk->indikatorKinerjaKegiatan->count()) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
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

            $ikk->name = $request->safe()['name'];
            $ikk->save();

            return redirect()->route('super-admin-iku-ikk', ['sk' => $skId]);
        }

        abort(404);
    }
}
