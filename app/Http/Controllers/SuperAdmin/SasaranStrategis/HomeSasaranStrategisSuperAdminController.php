<?php

namespace App\Http\Controllers\SuperAdmin\SasaranStrategis;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\RSYear;

class HomeSasaranStrategisSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $searchQuery = $request->query('search');

        $user = auth()->user();

        $time = RSYear::currentTime();

        $data = $time->sasaranStrategis()
            ->select([
                'number',
                'name',
                'id',
            ])
            ->withCount('kegiatan AS k');

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): Builder {
                return $query->where('name', 'LIKE', "%$searchQuery%");
            }
        );

        $data = $data->orderBy('number')->get()->toArray();

        $canDuplicate = false;

        if ($time->sasaranStrategis()->count() === 0) {
            if (RSYear::where('year', (string) (((int) $time->year) - 1))->first()?->sasaranStrategis()->count()) {
                $canDuplicate = true;
            }
        }

        return view('super-admin.rs.ss.home', compact([
            'canDuplicate',
            'searchQuery',
            'data',
            'user',
        ]));
    }
}
