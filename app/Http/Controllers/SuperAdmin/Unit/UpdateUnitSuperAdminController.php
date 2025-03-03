<?php

namespace App\Http\Controllers\SuperAdmin\Unit;

use App\Http\Controllers\_ControllerHelpers;
use App\Http\Requests\Units\EditRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\Unit;
use App\Models\User;

class UpdateUnitSuperAdminController extends Controller
{
    /**
     * @param \App\Models\Unit $unit
     * @return Factory|View
     */
    public function view(Unit $unit): Factory|View
    {
        $usersList = User::doesntHave('unit')
            ->where('role', 'admin')
            ->select([
                'name AS username',
                'access',
                'email',
                'id',
            ])
            ->get();

        $users = User::whereBelongsTo($unit)
            ->select([
                'name AS username',
                'access',
                'email',
                'id',
            ])
            ->selectRaw('true AS checked')
            ->get()
            ->merge($usersList)
            ->toArray();

        $data = $unit->only([
            'short_name',
            'name',
            'id',
        ]);

        return view('super-admin.unit.edit', compact([
            'users',
            'data',
        ]));
    }

    /**
     * @param \App\Http\Requests\Units\EditRequest $request
     * @param \App\Models\Unit $unit
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(EditRequest $request, Unit $unit): RedirectResponse
    {
        [
            'short_name' => $newShortName,
            'users' => $newUsers,
            'name' => $newName,
        ] = $request;

        if ($unit->name !== $newName) {
            $unit->name = $newName;
        }

        $unit->short_name = $newShortName;
        $unit->save();

        $oldUsers = $newUsers['old'] ?? [];
        $newUsers = $newUsers['new'] ?? [];

        $unit->users()->whereNotIn('id', $oldUsers)->each(function (User $user): void {
            $user->unit()->dissociate();
            $user->save();
        });

        $users = User::findMany($newUsers);

        foreach ($users as $key => $user) {
            $user->unit()->associate($unit);
            $user->save();
        }

        return _ControllerHelpers::RedirectWithRoute('super-admin-unit');
    }
}
