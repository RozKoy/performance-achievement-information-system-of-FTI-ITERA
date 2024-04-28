<?php

namespace App\Http\Controllers;

use App\Models\SasaranStrategis;
use Illuminate\Http\Request;

class SasaranStrategisController extends Controller
{
    public function addView()
    {
        $sasaranStrategis = SasaranStrategis::count() + 1;

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
}
