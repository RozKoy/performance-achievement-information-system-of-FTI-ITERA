<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaKegiatan\AddRequest;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\SasaranKegiatan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class IndikatorKinerjaKegiatanController extends Controller
{
    public function homeView(Request $request, $skId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);

        $data = $sk->indikatorKinerjaKegiatan()->select(['id', 'name', 'number'])
            ->where(function (Builder $query) use ($request) {
                if (isset ($request->search)) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('number', $request->search);
                }
            })
            ->withCount('programStrategis AS ps')
            ->orderBy('number')
            ->get()
            ->toArray();

        $sk = $sk->only(['id', 'name', 'number']);

        return view('super-admin.iku.ikk.home', compact(['data', 'sk']));
    }

    public function addView($skId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);

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

        $sk = $sk->only(['id', 'name', 'number']);

        return view('super-admin.iku.ikk.add', compact(['data', 'sk']));
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

        return redirect()->route('super-admin-iku-ikk', ['sk' => $sk->id]);
    }
}
