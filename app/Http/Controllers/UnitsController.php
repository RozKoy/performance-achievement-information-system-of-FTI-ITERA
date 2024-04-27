<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
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
        })->with('users')->get(['id', 'name'])->toArray();

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['users'] = count($data[$i]['users']);
        }

        return view('super-admin.unit.home', compact('data'));
    }

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

    public function editView($id)
    {
        $unit = Unit::whereKey($id)->with('users')->first(['id', 'name']);

        if ($unit !== null) {
            $user_list = User::where('role', 'admin')->whereNull('unit_id')->get(['id', 'name AS username', 'email', 'access'])->toArray();

            $users = $unit->users()->get(['id', 'name AS username', 'email', 'access'])->toArray();

            $data = $unit->toArray();
            unset($data['users']);

            return view('super-admin.unit.edit', compact(['data', 'users', 'user_list']));
        }

        abort(404);
    }
}