<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProgramStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class ProgramStrategisController extends Controller
{
    public function homeView(Request $request, $skId, $ikkId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);
        $sk->indikatorKinerjaKegiatan()->findOrFail($ikkId);

        $ikk = IndikatorKinerjaKegiatan::findOrFail($ikkId);

        $data = $ikk->programStrategis()->select(['id', 'name', 'number'])
            ->where(function (Builder $query) use ($request) {
                if (isset ($request->search)) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('number', $request->search);
                }
            })
            ->withCount([
                'indikatorKinerjaProgram AS active' =>
                    function (Builder $query) {
                        $query->where('status', 'aktif');
                    },
                'indikatorKinerjaProgram AS inactive' =>
                    function (Builder $query) {
                        $query->where('status', 'tidak aktif');
                    }
            ])
            ->orderBy('number')
            ->get()
            ->toArray();

        $ikk = $ikk->only(['id', 'name', 'number']);
        $sk = $sk->only(['id', 'name', 'number']);

        return view('super-admin.iku.ps.home', compact('data', 'ikk', 'sk'));
    }

    public function addView($skId, $ikkId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);
        $sk->indikatorKinerjaKegiatan()->findOrFail($ikkId);

        $ikk = IndikatorKinerjaKegiatan::findOrFail($ikkId);

        $count = $ikk->programStrategis->count() + 1;

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

        $sk = $sk->only(['id', 'name', 'number']);
        $ikk = $ikk->only(['id', 'name', 'number']);

        return view('super-admin.iku.ps.add', compact(['data', 'ikk', 'sk']));
    }

    public function add(AddRequest $request, $skId, $ikkId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);
        $sk->indikatorKinerjaKegiatan()->findOrFail($ikkId);

        $ikk = IndikatorKinerjaKegiatan::findOrFail($ikkId);

        $number = $request->safe()['number'];
        $dataCount = $ikk->programStrategis->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $ikk->programStrategis()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $ps = new ProgramStrategis($request->safe()->all());

        $ps->indikatorKinerjaKegiatan()->associate($ikk);

        $ps->save();

        return redirect()->route('super-admin-iku-ps', ['sk' => $skId, 'ikk' => $ikkId]);
    }
}
