<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerja\AddRequest;
use App\Http\Requests\IndikatorKinerja\EditRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SasaranStrategis;
use App\Models\IndikatorKinerja;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class IndikatorKinerjaController extends Controller
{
    protected $type = [
        [
            'value' => 'teks',
            'text' => 'Teks',
        ],
        [
            'value' => 'angka',
            'text' => 'Angka',
        ],
        [
            'value' => 'persen',
            'text' => 'Persen',
        ],
    ];

    public function homeView(Request $request, $ssId, $kId)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);
        $ss->kegiatan()->findOrFail($kId);

        $k = Kegiatan::findOrFail($kId);

        $data = $k->indikatorKinerja()->select(['id', 'name', 'type', 'status', 'number'])
            ->where(function (Builder $query) use ($request) {
                if (isset ($request->search)) {
                    $query->where('name', 'LIKE', "%{$request->search}%")
                        ->orWhere('type', $request->search)
                        ->orWhere('status', $request->search)
                        ->orWhere('number', $request->search);
                }
            })
            ->orderBy('number')
            ->get()
            ->toArray();

        $ss = $ss->only(['id', 'name', 'number']);
        $k = $k->only(['id', 'name', 'number']);

        return view('super-admin.rs.ik.home', compact(['data', 'ss', 'k']));
    }

    public function addView($ssId, $kId)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);
        $ss->kegiatan()->findOrFail($kId);

        $k = Kegiatan::findOrFail($kId);

        $count = $k->indikatorKinerja->count() + 1;

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
        $k = $k->only(['id', 'name', 'number']);
        $type = $this->type;

        return view('super-admin.rs.ik.add', compact(['type', 'data', 'ss', 'k']));
    }

    public function add(AddRequest $request, $ssId, $kId)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);
        $ss->kegiatan()->findOrFail($kId);

        $k = Kegiatan::findOrFail($kId);

        $number = $request->safe()['number'];
        $dataCount = $k->indikatorKinerja->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $k->indikatorKinerja()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $indikatorKinerja = new IndikatorKinerja($request->safe()->all());

        $indikatorKinerja->kegiatan()->associate($k);
        $indikatorKinerja->status = 'aktif';

        $indikatorKinerja->save();

        return redirect()->route('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id]);
    }

    public function editView($ssId, $kId, $id)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);
        $ss->kegiatan()->findOrFail($kId);

        $k = Kegiatan::findOrFail($kId);
        $ik = $k->indikatorKinerja()->findOrFail($id);

        $count = $k->indikatorKinerja->count();

        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $data[$i] = [
                "value" => strval($i + 1),
                "text" => strval($i + 1),
            ];
        }
        $data[$ik->number - 1] = [
            ...$data[$ik->number - 1],
            'selected' => true,
        ];

        $type = array_map(function ($item) use ($ik) {
            if ($ik->type === $item['value']) {
                $item['selected'] = true;
            }
            return $item;
        }, $this->type);

        $ss = $ss->only(['id', 'name', 'number']);
        $k = $k->only(['id', 'name', 'number']);
        $ik = $ik->only(['id', 'name']);

        return view('super-admin.rs.ik.edit', compact('data', 'type', 'ss', 'k', 'ik'));
    }

    public function edit(EditRequest $request, $ssId, $kId, $id)
    {
        $ss = SasaranStrategis::findOrFail($ssId);
        $ss->kegiatan()->findOrFail($kId);

        $k = Kegiatan::findOrFail($kId);
        $k->indikatorKinerja()->findOrFail($id);

        $ik = IndikatorKinerja::findOrFail($id);

        $number = (int) $request->safe()['number'];
        if ($number > $k->indikatorKinerja->count()) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        $currentNumber = $ik->number;
        if ($number !== $currentNumber) {
            $ik->number = $number;

            if ($number < $currentNumber) {
                $k->indikatorKinerja()
                    ->where('number', '>=', $number)
                    ->where('number', '<', $currentNumber)
                    ->increment('number');
            } else {
                $k->indikatorKinerja()
                    ->where('number', '<=', $number)
                    ->where('number', '>', $currentNumber)
                    ->decrement('number');
            }
        }

        $ik->name = $request->safe()['name'];
        $ik->save();

        return redirect()->route('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id]);
    }

    public function statusToggle($ssId, $kId, $id)
    {
        $ss = SasaranStrategis::currentOrFail($ssId);
        $ss->kegiatan()->findOrFail($kId);

        $k = Kegiatan::findOrFail($kId);
        $k->indikatorKinerja()->findOrFail($id);

        $ik = IndikatorKinerja::findOrFail($id);

        $newStatus = $ik->status === 'aktif' ? 'tidak aktif' : 'aktif';
        $ik->status = $newStatus;

        $ik->save();

        return redirect()->route('super-admin-rs-ik', ['ss' => $ss->id, 'k' => $k->id]);
    }
}
