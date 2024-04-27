<?php

namespace App\Http\Controllers;

use App\Http\Requests\Units\AddRequest;
use App\Models\Unit;
use App\Models\User;

class UnitsController extends Controller
{
    public function addView()
    {
        $users = User::where('role', 'admin')->whereNull('unit_id')->get(['id', 'name AS username', 'email', 'access'])->toArray();

        return view('super-admin.unit.add', compact('users'));
    }

    public function add(AddRequest $request)
    {
        $unit = Unit::create($request->safe()->only('name'));

        if (isset($request->safe()['users'])) {
            $users = User::findMany($request->safe()['users']);

            foreach ($users as $key => $user) {
                $user->unit()->associate($unit);
                $user->save();
            }
        }

        return redirect()->route('super-admin-unit');
    }
}
