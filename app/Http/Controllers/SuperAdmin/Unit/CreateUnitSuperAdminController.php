<?php

namespace App\Http\Controllers\SuperAdmin\Unit;

use App\Http\Controllers\_ControllerHelpers;
use App\Http\Requests\Units\AddRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\Unit;
use App\Models\User;

class CreateUnitSuperAdminController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        $users = User::where('role', 'admin')
            ->doesntHave('unit')
            ->select([
                'name AS username',
                'access',
                'email',
                'id',
            ])
            ->get()
            ->toArray();

        return view('super-admin.unit.add', compact('users'));
    }

    /**
     * @param \App\Http\Requests\Units\AddRequest $request
     * @return RedirectResponse
     */
    public function action(AddRequest $request): RedirectResponse
    {
        $unitExists = Unit::withTrashed()->firstWhere('name', $request['name']);

        if ($unitExists) {
            if ($unitExists->deleted_at) {
                $unitExists->restore();
            } else {
                return _ControllerHelpers::BackWithInputWithErrors(['name' => 'Nama unit sudah digunakan']);
            }

            $unit = $unitExists;
        } else {
            $unit = Unit::create($request->safe()->only('short_name', 'name'));
        }

        if (isset($request->safe()['users'])) {
            $users = User::findMany($request['users']);

            foreach ($users as $key => $user) {
                $user->unit()->associate($unit);
                $user->save();
            }
        }

        return _ControllerHelpers::RedirectWithRoute('super-admin-unit')->with('success', 'Berhasil menambahkan unit');
    }
}
