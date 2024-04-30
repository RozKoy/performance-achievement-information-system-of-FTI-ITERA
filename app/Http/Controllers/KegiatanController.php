<?php

namespace App\Http\Controllers;

use App\Models\SasaranStrategis;
use Illuminate\Http\Request;

class KegiatanController extends Controller
{
    public function addView($ssId)
    {
        $ss = SasaranStrategis::findOrFail($ssId, ['id', 'name', 'number']);

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

        $ss = $ss->toArray();

        return view('super-admin.rs.k.add', compact(['data', 'ss']));
    }
}
