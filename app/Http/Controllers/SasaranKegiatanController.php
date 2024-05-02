<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranKegiatan\AddRequest;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;
use App\Models\IKUTime;

class SasaranKegiatanController extends Controller
{
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
}
