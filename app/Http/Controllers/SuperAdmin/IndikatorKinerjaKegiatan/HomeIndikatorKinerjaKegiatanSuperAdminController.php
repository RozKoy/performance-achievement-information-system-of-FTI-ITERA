<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaKegiatan;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class HomeIndikatorKinerjaKegiatanSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranKegiatan $sk
     * @return Factory|View
     */
    public function view(Request $request, SasaranKegiatan $sk): Factory|View
    {
        $searchQuery = $request->query('search');

        $user = auth()->user();

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $data = $sk->indikatorKinerjaKegiatan()
            ->select([
                'number',
                'name',
                'id',
            ])
            ->withCount('programStrategis AS ps');

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): Builder {
                return $query->where('name', 'LIKE', "%$searchQuery%");
            }
        );

        $data = $data->orderBy('number')->get()->toArray();

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.iku.ikk.home', compact([
            'searchQuery',
            'data',
            'user',
            'sk',
        ]));
    }
}
