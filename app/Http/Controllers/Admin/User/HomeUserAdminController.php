<?php

namespace App\Http\Controllers\Admin\User;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\User;

class HomeUserAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $search = $request->query('search');

        $user = auth()->user();

        $data = User::query()
            ->select([
                'access',
                'email',
                'name',
                'id',
            ])
            ->whereKeyNot($user->id)
            ->where('unit_id', $user->unit->id);

        $data->when(
            $search !== null,
            function (Builder $query) use ($search): Builder {
                return $query->whereAny(
                    [
                        'access',
                        'email',
                        'name',
                    ],
                    'LIKE',
                    "%$search%",
                );
            }
        );

        $data = $data->latest()->get();

        return view('admin.users.home', compact([
            'search',
            'data',
            'user',
        ]));
    }
}
