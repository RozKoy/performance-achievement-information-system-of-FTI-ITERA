<?php

namespace App\Http\Controllers;

use App\Http\Requests\SasaranStrategis\AddRequest;
use App\Models\SasaranStrategis;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSTime;

class SasaranStrategisController extends Controller
{
    function getCurrentTime(): RSTime
    {
        $period = (int) Carbon::now()->format('m') <= 6 ? '1' : '2';
        $year = Carbon::now()->format('Y');

        return RSTime::firstOrCreate(['period' => $period, 'year' => $year], ['status' => 'aktif']);
    }

    public function homeView(Request $request)
    {
        $time = $this->getCurrentTime();

        $data = $time->sasaranStrategis()->select(['id', 'name', 'number'])
            ->where(function (Builder $query) use ($request) {
                if (isset ($request->search)) {
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
        $time = $this->getCurrentTime();

        $sasaranStrategis = $time->sasaranStrategis->count() + 1;

        $data = [];
        for ($i = 0; $i < $sasaranStrategis; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$sasaranStrategis - 1] = [
            ...$data[$sasaranStrategis - 1],
            'selected' => true,
        ];

        return view('super-admin.rs.ss.add', compact('data'));
    }

    public function add(AddRequest $request)
    {
        $time = $this->getCurrentTime();

        $number = (int) $request->safe()['number'];
        $jumlahData = $time->sasaranStrategis->count();
        if ($number > $jumlahData + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $jumlahData) {
            $time->sasaranStrategis()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $sasaranStrategis = new SasaranStrategis($request->safe()->all());

        $sasaranStrategis->deadline()->associate($time);
        $sasaranStrategis->time()->associate($time);

        $sasaranStrategis->save();

        return redirect()->route('super-admin-rs-ss');
    }

    public function editView($id)
    {
        $sasaranStrategis = SasaranStrategis::whereKey($id)
            ->firstOrFail(['id', 'name', 'number', 'time_id']);

        $count = $sasaranStrategis->time->sasaranStrategis->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$sasaranStrategis->number - 1] = [
            ...$data[$sasaranStrategis->number - 1],
            'selected' => true,
        ];

        $sasaranStrategis = $sasaranStrategis->only(['id', 'name']);

        return view('super-admin.rs.ss.edit', compact(['data', 'sasaranStrategis']));
    }
}
