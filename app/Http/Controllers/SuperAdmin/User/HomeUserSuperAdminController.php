<?php

namespace App\Http\Controllers\SuperAdmin\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\User;

class HomeUserSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $searchQuery = $request->query('search');

        $user = auth()->user();

        $data = User::query()
            ->whereKeyNot($user->id)
            ->select([
                'access',
                'email',
                'name',
                'role',
                'id',
            ])
            ->withAggregate('unit AS unit', 'name');

        $data->when(
            $searchQuery !== null,
            function (Builder $query) use ($searchQuery): Builder {
                return $query->whereAny(
                    [
                        'access',
                        'email',
                        'name',
                        'role',
                    ],
                    'LIKE',
                    "%$searchQuery%"
                )
                    ->orWhereRelation('unit', 'name', 'LIKE', "%$searchQuery%");
            }
        );

        $data = $data->latest()->get()->toArray();

        return view('super-admin.users.home', compact([
            'searchQuery',
            'data',
            'user',
        ]));
    }
}
