<?php

namespace App\Http\Controllers\SuperAdmin\Unit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Unit;

class HomeUnitSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $search = $request->query('search');

        $user = auth()->user();

        $data = Unit::query()
            ->select([
                'short_name',
                'name',
                'id',
            ])
            ->withCount('users AS users');

        $data->when(
            $search !== null,
            function (Builder $query) use ($search): Builder {
                return $query->whereAny(
                    [
                        'short_name',
                        'name'
                    ],
                    'LIKE',
                    "%$search%"
                );
            }
        );

        $data = $data->latest()->get()->toArray();

        return view('super-admin.unit.home', compact([
            'search',
            'data',
            'user',
        ]));
    }
}
