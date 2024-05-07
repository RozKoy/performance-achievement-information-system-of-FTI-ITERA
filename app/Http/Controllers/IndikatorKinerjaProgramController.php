<?php

namespace App\Http\Controllers;

use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class IndikatorKinerjaProgramController extends Controller
{
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

        $types = [
            [
                'value' => 'iku',
                'text' => 'IKU',
                'selected' => true,
            ],
            [
                'value' => 'ikt',
                'text' => 'IKT',
            ],
        ];

        $sk = $sk->only(['id', 'name', 'number']);
        $ikk = $ikk->only(['id', 'name', 'number']);
        $ps = $ps->only(['id', 'name', 'number']);

        return view('super-admin.iku.ikp.add', compact(['types', 'data', 'sk', 'ikk', 'ps']));
    }
}
