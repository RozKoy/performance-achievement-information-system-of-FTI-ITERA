<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerja;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\SasaranStrategis;
use Illuminate\Http\Request;
use App\Models\Kegiatan;

class HomeIndikatorKinerjaSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranStrategis $ss
     * @param \App\Models\Kegiatan $k
     * @return Factory|View
     */
    public function view(Request $request, SasaranStrategis $ss, Kegiatan $k): Factory|View
    {
        $searchQuery = $request->query('search');

        if ($ss->id !== $k->sasaranStrategis->id) {
            abort(404);
        }

        $user = auth()->user();

        $ss = SasaranStrategis::currentOrFail($ss->id);

        $data = $k->indikatorKinerja()
            ->select([
                'number',
                'status',
                'name',
                'type',
                'id',
            ]);

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): Builder {
                return $query->whereAny(
                    [
                        'status',
                        'name',
                        'type',
                    ],
                    'LIKE',
                    "%$searchQuery%"
                );
            }
        );

        $data = $data->orderBy('number')->get()->toArray();

        $ss = $ss->only([
            'number',
            'name',
            'id',
        ]);
        $k = $k->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.rs.ik.home', compact([
            'searchQuery',
            'data',
            'user',
            'ss',
            'k',
        ]));
    }
}
