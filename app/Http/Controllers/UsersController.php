<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\EditAdminRequest;
use App\Http\Requests\Users\AddAdminRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Users\EditRequest;
use App\Http\Requests\Users\AddRequest;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;

class UsersController extends Controller
{
    /*
    | -----------------------------------------------------------------
    | SUPER ADMIN
    | -----------------------------------------------------------------
    */

    public function homeView(Request $request)
    {
        $data = User::whereKeyNot(auth()->user()->id)
            ->doesntHave('unit')
            ->where(function (Builder $query) use ($request) {
                if ($request->search) {
                    $query->whereAny(
                        [
                            'access',
                            'email',
                            'name',
                            'role',
                        ],
                        'LIKE',
                        "%{$request->search}%",
                    );
                }
            })
            ->orWhereHas('unit', function (Builder $query) use ($request) {
                if ($request->search) {
                    $query->whereAny(
                        [
                            'access',
                            'email',
                            'name',
                            'role',
                        ],
                        'LIKE',
                        "%{$request->search}%",
                    );
                }
            })
            ->select([
                'access',
                'email',
                'name',
                'role',
                'id',
            ])
            ->withAggregate('unit AS unit', 'name')
            ->latest()
            ->get()
            ->toArray();

        return view('super-admin.users.home', compact('data'));
    }

    public function addView()
    {
        $units = Unit::select([
            'name AS text',
            'id AS value',
        ])
            ->get()
            ->toArray();

        $data = [
            [
                'value' => '',
                'text' => 'Pilih Unit'
            ],
            ...$units
        ];

        return view('super-admin.users.add', compact('data'));
    }

    public function add(AddRequest $request)
    {
        if (in_array($request['access'], ['super-admin-editor', 'super-admin-viewer'])) {
            $request['role'] = 'super admin';
            $request['unit_id'] = null;

            $access = 'viewer';
            if ($request['access'] === 'super-admin-editor') {
                $access = 'editor';
            }
            $request['access'] = $access;
        } else {
            $request['role'] = 'admin';
            $request['unit_id'] = null;

            $access = 'viewer';
            if (!isset($request['access'])) {
                $access = 'editor';
            }
            $request['access'] = $access;

            if (isset($request['unit'])) {
                $request['unit_id'] = $request['unit'];
            }
        }
        $request['password'] = str_replace(" ", "_", $request['name']);

        User::create($request->only([
            'password',
            'access',
            'email',
            'name',
            'role',

            'unit_id',
        ]));

        return redirect()->route('super-admin-users');
    }

    public function editView($id)
    {
        $user = User::whereKey($id)
            ->firstOrFail(['id', 'name', 'email', 'role', 'access', 'unit_id']);

        $units = Unit::get(['id AS value', 'name AS text'])->toArray();

        $unitId = $user->unit_id;
        if ($unitId !== null) {
            $units = array_map(function ($unit) use ($unitId) {
                if ($unit['value'] === $unitId) {
                    $unit = [
                        ...$unit,
                        'selected' => true
                    ];
                }
                return $unit;
            }, $units);
        }

        $data = [
            [
                'value' => '',
                'text' => 'Pilih Unit'
            ],
            ...$units
        ];

        $user = $user->makeHidden('unit_id')->toArray();

        return view('super-admin.users.edit', compact(['user', 'data']));
    }

    public function edit(EditRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $newEmail = $request->safe()['email'];

        if ($user->email !== $newEmail) {
            $user->email = $newEmail;
        }

        $newName = $request->safe()['name'];

        if ($user->name !== $newName) {
            $user->name = $newName;
        }

        if (in_array($request['access'], ['super-admin-editor', 'super-admin-viewer'])) {
            $user->role = 'super admin';
            $user->unit_id = null;

            $user->access = 'viewer';
            if ($request['access'] === 'super-admin-editor') {
                $user->access = 'editor';
            }
        } else {
            $user->role = 'admin';

            $user->access = 'viewer';
            if (!isset($request['access'])) {
                $user->access = 'editor';
            }

            $user->unit_id = null;
            if (isset($request['unit'])) {
                $user->unit_id = $request['unit'];
            }
        }

        $user->save();

        return redirect()->route('super-admin-users');
    }


    /*
    | -----------------------------------------------------------------
    | ADMIN
    | -----------------------------------------------------------------
    */

    public function homeViewAdmin(Request $request)
    {
        $data = auth()->user()
            ->unit
            ->users()
            ->whereKeyNot(auth()->user()->id)
            ->where(function (Builder $query) use ($request) {
                if ($request->search) {
                    $query->whereAny(
                        [
                            'access',
                            'email',
                            'name',
                            'role',
                        ],
                        'LIKE',
                        "%{$request->search}%",
                    );
                }
            })
            ->select(['id', 'name', 'email', 'access'])
            ->latest()
            ->get()
            ->toArray();

        return view('admin.users.home', compact('data'));
    }

    public function addViewAdmin()
    {
        return view('admin.users.add');
    }

    public function addAdmin(AddAdminRequest $request)
    {
        User::create(
            [
                ...$request->safe()->all(),
                'password' => str_replace(" ", "_", $request->safe()['name']),
                'unit_id' => auth()->user()->unit_id,
                'role' => 'admin'
            ]
        );

        return redirect()->route('admin-users');
    }

    public function editViewAdmin($id)
    {
        $user = auth()->user()
            ->unit
            ->users()
            ->findOrFail($id);

        $user = $user->only(['id', 'name', 'email', 'access']);

        return view('admin.users.edit', compact('user'));
    }

    public function editAdmin(EditAdminRequest $request, $id)
    {
        $user = User::findOrFail($id);

        $newEmail = $request->safe()['email'];

        if ($user->email !== $newEmail) {
            $user->email = $newEmail;
        }

        $newName = $request->safe()['name'];

        if ($user->name !== $newName) {
            $user->name = $newName;
        }

        $newAccess = $request->safe()['access'];

        if ($user->access !== $newAccess) {
            $user->access = $newAccess;
        }

        $user->save();

        return redirect()->route('admin-users');
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);

        $user->forceDelete();

        return back();
    }
}
