<?php

namespace App\Http\Controllers\SuperAdmin\SasaranKegiatan;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\IKUYear;

class HomeSasaranKegiatanSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $searchQuery = $request->query('search');

        $user = auth()->user();

        $time = IKUYear::currentTime();

        $data = $time->sasaranKegiatan()
            ->select([
                'number',
                'name',
                'id',
            ])
            ->withCount('indikatorKinerjaKegiatan AS ikk');

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): Builder {
                return $query->where('name', 'LIKE', "%$searchQuery%");
            }
        );

        $data = $data->orderBy('number')->get()->toArray();

        $canDuplicate = false;

        if ($time->sasaranKegiatan()->count() === 0) {
            if (IKUYear::where('year', (string) (((int) $time->year) - 1))->first()?->sasaranKegiatan()->count()) {
                $canDuplicate = true;
            }
        }

        return view('super-admin.iku.sk.home', compact([
            'canDuplicate',
            'searchQuery',
            'data',
            'user',
        ]));
    }
}
