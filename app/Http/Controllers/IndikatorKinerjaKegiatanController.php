<?php

namespace App\Http\Controllers;

use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class IndikatorKinerjaKegiatanController extends Controller
{
    public function addView($skId)
    {
        $sk = SasaranKegiatan::currentOrFail($skId);

        $count = $sk->indikatorKinerjaKegiatan->count() + 1;

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

        $sk = $sk->only(['id', 'name', 'number']);

        return view('super-admin.iku.ikk.add', compact(['data', 'sk']));
    }
}
