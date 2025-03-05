<?php

namespace App\Http\Controllers\SuperAdmin\ProgramStrategis;

use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\SasaranKegiatan;
use Illuminate\Http\Request;

class HomeProgramStrategisSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\SasaranKegiatan $sk
     * @param \App\Models\IndikatorKinerjaKegiatan $ikk
     * @return Factory|View
     */
    public function view(Request $request, SasaranKegiatan $sk, IndikatorKinerjaKegiatan $ikk): Factory|View
    {
        $searchQuery = $request->query('search');

        if ($sk->id !== $ikk->sasaranKegiatan->id) {
            abort(404);
        }

        $user = auth()->user();

        $sk = SasaranKegiatan::currentOrFail($sk->id);

        $data = $ikk->programStrategis()
            ->select([
                'number',
                'name',
                'id',
            ])
            ->withCount([
                'indikatorKinerjaProgram AS active' => function (Builder $query): void {
                    $query->where('status', 'aktif');
                },
                'indikatorKinerjaProgram AS inactive' => function (Builder $query): void {
                    $query->where('status', 'tidak aktif');
                }
            ]);

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): void {
                $query->where('name', 'LIKE', "%$searchQuery%");
            }
        );

        $data = $data->orderBy('number')->get()->toArray();

        $ikk = $ikk->only([
            'number',
            'name',
            'id',
        ]);
        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);

        return view('super-admin.iku.ps.home', compact([
            'searchQuery',
            'data',
            'user',
            'ikk',
            'sk',
        ]));
    }
}
