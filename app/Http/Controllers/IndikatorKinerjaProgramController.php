<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaProgram\EditRequest;
use App\Http\Requests\IndikatorKinerjaProgram\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class IndikatorKinerjaProgramController extends Controller
{
    protected $types = [
        [
            'value' => 'iku',
            'text' => 'IKU',
        ],
        [
            'value' => 'ikt',
            'text' => 'IKT',
        ],
    ];

    public function homeView(Request $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk, ProgramStrategis $ps)
    {
        if ($sk->id === $ikk->sasaranKegiatan->id && $ikk->id === $ps->indikatorKinerjaKegiatan->id) {
            $sk = SasaranKegiatan::currentOrFail($sk->id);

            $data = $ps->indikatorKinerjaProgram()
                ->select([
                    'definition',
                    'number',
                    'status',
                    'name',
                    'type',
                    'id',
                ])
                ->where(function (Builder $query) use ($request) {
                    if (isset($request->search)) {
                        $query->where('name', 'LIKE', "%{$request->search}%")
                            ->orWhere('definition', 'LIKE', "%{$request->search}%")
                            ->orWhere('number', $request->search)
                            ->orWhere('status', $request->search)
                            ->orWhere('type', $request->search);
                    }
                })
                ->withCount('columns AS column')
                ->orderBy('number')
                ->get()
                ->toArray();

            $sk = $sk->only([
                'number',
                'name',
                'id',
            ]);
            $ikk = $ikk->only([
                'number',
                'name',
                'id',
            ]);
            $ps = $ps->only([
                'number',
                'name',
                'id',
            ]);

            return view('super-admin.iku.ikp.home', compact([
                'data',
                'ikk',
                'ps',
                'sk',
            ]));
        }

        abort(404);
    }

    public function addView($skId, $ikkId, $psId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);
        $ikk = $sk->indikatorKinerjaKegiatan()->findOrFail($ikkId);
        $ps = $ikk->programStrategis()->findOrFail($psId);

        $count = $ps->indikatorKinerjaProgram->count() + 1;

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

        $types = $this->types;
        $types[0] = [
            ...$types[0],
            'selected' => true
        ];

        $sk = $sk->only(['id', 'name', 'number']);
        $ikk = $ikk->only(['id', 'name', 'number']);
        $ps = $ps->only(['id', 'name', 'number']);

        return view('super-admin.iku.ikp.add', compact(['types', 'data', 'sk', 'ikk', 'ps']));
    }

    public function add(AddRequest $request, $skId, $ikkId, $psId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);
        $ikk = $sk->indikatorKinerjaKegiatan()->findOrFail($ikkId);
        $ps = $ikk->programStrategis()->findOrFail($psId);

        $number = $request->safe()['number'];
        $dataCount = $ps->indikatorKinerjaProgram->count();
        if ($number > $dataCount + 1) {
            return back()
                ->withInput()
                ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
        }

        if ($number <= $dataCount) {
            $ps->indikatorKinerjaProgram()
                ->where('number', '>=', $number)
                ->increment('number');
        }

        $ikp = new IndikatorKinerjaProgram($request->safe()->except('columns', 'file'));

        $ikp->programStrategis()->associate($ps);
        $ikp->status = 'aktif';

        $ikp->save();

        $index = 1;
        foreach ($request['columns'] as $value) {
            $ikp->columns()->create([
                'number' => $index,
                'name' => $value
            ]);

            $index++;
        }

        if ($request['file'] !== null) {
            $ikp->columns()->create([
                'name' => $request['file'],
                'number' => $index,
                'file' => true
            ]);
        }

        return redirect()->route('super-admin-iku-ikp', ['sk' => $skId, 'ikk' => $ikkId, 'ps' => $psId]);
    }

    public function editView($skId, $ikkId, $psId, $id)
    {
        $ikp = IndikatorKinerjaProgram::findOrFail($id);

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        if ($ps->id === $psId && $ikk->id === $ikkId && $sk->id === $skId) {
            $count = $ps->indikatorKinerjaProgram->count();
            $data = [];
            for ($i = 0; $i < $count; $i++) {
                $data[$i] = [
                    "value" => strval($i + 1),
                    "text" => strval($i + 1),
                ];
            }
            $data[$ikp->number - 1] = [
                ...$data[$ikp->number - 1],
                'selected' => true,
            ];

            $index = $ikp->type === 'iku' ? 0 : 1;
            $types = $this->types;
            $types[$index] = [
                ...$types[$index],
                'selected' => true
            ];

            $sk = $sk->only(['id', 'name', 'number']);
            $ikk = $ikk->only(['id', 'name', 'number']);
            $ps = $ps->only(['id', 'name', 'number']);
            $ikp = $ikp->only(['id', 'name', 'status', 'column', 'definition']);

            return view('super-admin.iku.ikp.edit', compact(['types', 'data', 'sk', 'ikk', 'ps', 'ikp']));
        }

        abort(404);
    }

    public function edit(EditRequest $request, $skId, $ikkId, $psId, $id)
    {
        $ikp = IndikatorKinerjaProgram::findOrFail($id);

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        if ($ps->id === $psId && $ikk->id === $ikkId && $sk->id === $skId) {
            $number = (int) $request->safe()['number'];
            if ($number > $ps->indikatorKinerjaProgram->count()) {
                return back()
                    ->withInput()
                    ->withErrors(['number' => 'Nomor tidak sesuai dengan jumlah data']);
            }

            $currentNumber = $ikp->number;
            if ($number !== $currentNumber) {
                $ikp->number = $number;

                if ($number < $currentNumber) {
                    $ps->indikatorKinerjaProgram()
                        ->where('number', '>=', $number)
                        ->where('number', '<', $currentNumber)
                        ->increment('number');
                } else {
                    $ps->indikatorKinerjaProgram()
                        ->where('number', '<=', $number)
                        ->where('number', '>', $currentNumber)
                        ->decrement('number');
                }
            }

            $ikp->name = $request->safe()['name'];
            $ikp->type = $request->safe()['type'];
            $ikp->definition = $request->safe()['definition'];
            $ikp->column = json_encode($request->safe()['columns']);

            $ikp->save();

            return redirect()->route('super-admin-iku-ikp', ['sk' => $skId, 'ikk' => $ikkId, 'ps' => $psId]);
        }

        abort(404);
    }

    public function statusToggle($skId, $ikkId, $psId, $id)
    {
        $ikp = IndikatorKinerjaProgram::findOrFail($id);

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        if ($ps->id === $psId && $ikk->id === $ikkId && $sk->id === $skId) {
            $newStatus = $ikp->status === 'aktif' ? 'tidak aktif' : 'aktif';
            $ikp->status = $newStatus;

            $ikp->save();

            return back();
        }

        abort(404);
    }
}
