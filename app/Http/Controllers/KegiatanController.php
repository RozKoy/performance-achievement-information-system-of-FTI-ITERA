<?php

namespace App\Http\Controllers;

use App\Models\SasaranStrategis;
use Illuminate\Http\Request;

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
}
