<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Units\EditRequest;
use App\Http\Requests\Units\AddRequest;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;

class UnitsController extends Controller
{
    public function homeView(Request $request)
    {
        $data = Unit::where(function (Builder $query) use ($request) {
            if ($request->search) {
                $query->where('name', 'LIKE', "%{$request->search}%");
            }
        })->select(['id', 'name'])
            ->withCount('users AS users')
            ->latest()
            ->get()
            ->toArray();

        return view('super-admin.unit.home', compact('data'));
    }

    public function addView()
    {
        $users = User::where('role', 'admin')
            ->whereNull('unit_id')
            ->get(['id', 'name AS username', 'email', 'access'])
            ->toArray();

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

    public function editView($id)
    {
        $unit = Unit::whereKey($id)
            ->with('users')
            ->firstOrFail(['id', 'name']);

        $usersExists = $unit->users()
            ->select(['id', 'name AS username', 'email', 'access'])
            ->selectRaw('true AS checked')
            ->get()
            ->toArray();
        $usersList = User::where('role', 'admin')
            ->whereNull('unit_id')
            ->get(['id', 'name AS username', 'email', 'access'])
            ->toArray();

        $users = array_merge($usersExists, $usersList);

        $data = $unit->only(['id', 'name']);

        return view('super-admin.unit.edit', compact(['data', 'users']));
    }

    public function edit(EditRequest $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $newName = $request->safe()['name'];

        if ($unit->name !== $newName) {
            $temp = Unit::whereKeyNot($id)
                ->where('name', $newName)
                ->first();

            if ($temp !== null) {
                return back()->withInput()->withErrors(['name' => 'Nama unit sudah digunakan']);
            }

            $unit->name = $newName;
            $unit->save();
        }

        $oldUsers = [];
        $newUsers = [];

        if (isset($request->safe()['users'])) {
            if (isset($request->safe()['users']['old'])) {
                $oldUsers = $request->safe()['users']['old'];
            }
            if (isset($request->safe()['users']['new'])) {
                $newUsers = $request->safe()['users']['new'];
            }
        }

        $unit->users()->each(function ($user) use ($oldUsers) {
            if (!in_array($user->id, $oldUsers)) {
                $user->unit()->dissociate();
                $user->save();
            }
        });

        $users = User::findMany($newUsers);

        foreach ($users as $key => $user) {
            $user->unit()->associate($unit);
            $user->save();
        }

        return redirect()->route('super-admin-unit');
    }
}
