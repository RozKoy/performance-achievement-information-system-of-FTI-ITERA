<?php

namespace App\Http\Controllers\SuperAdmin\Kegiatan;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\SasaranStrategis;
use Illuminate\Http\Request;

class HomeKegiatanSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranStrategis $ss
     * @return Factory|View
     */
    public function view(Request $request, SasaranStrategis $ss): Factory|View
    {
        $searchQuery = $request->query('search');

        $user = auth()->user();

        $ss = SasaranStrategis::currentOrFail($ss->id);

        $data = $ss->kegiatan()
            ->select([
                'number',
                'name',
                'id',
            ])
            ->withCount([
                'indikatorKinerja AS active' => function (Builder $query): void {
                    $query->where('status', 'aktif');
                },
                'indikatorKinerja AS inactive' => function (Builder $query): void {
                    $query->where('status', 'tidak aktif');
                }
            ]);

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): Builder {
                return $query->where('name', 'LIKE', "%$searchQuery%");
            }
        );

        $data = $data->orderBy('number')->get()->toArray();

        $ss = $ss->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.rs.k.home', compact([
            'searchQuery',
            'data',
            'user',
            'ss',
        ]));
    }
}
