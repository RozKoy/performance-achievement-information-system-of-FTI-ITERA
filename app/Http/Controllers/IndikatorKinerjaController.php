<?php

namespace App\Http\Controllers;

use App\Models\SasaranStrategis;
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
}
