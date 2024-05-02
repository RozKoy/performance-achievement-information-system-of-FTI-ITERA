<?php

namespace App\Http\Controllers;

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
}
