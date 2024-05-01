<?php

namespace App\Http\Controllers;

use App\Http\Requests\Kegiatan\AddRequest;
use App\Models\SasaranStrategis;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class KegiatanController extends Controller
{
    public function addView($ssId)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);

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

        $ss = $ss->only(['id', 'name', 'number']);

        return view('super-admin.rs.k.add', compact(['data', 'ss']));
    }

    public function add(AddRequest $request, $ssId)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);

        $number = $request->safe()['number'];
        $dataCount = $ss->kegiatan->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $ss->kegiatan()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $kegiatan = new Kegiatan($request->safe()->all());

        $kegiatan->sasaranStrategis()->associate($ss);

        $kegiatan->save();

        return redirect()->route('super-admin-rs-k', ['ss' => $ss->id]);
    }
}
