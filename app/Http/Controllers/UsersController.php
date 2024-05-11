<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\AddAdminRequest;
use App\Http\Requests\Users\EditAdminRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Users\EditRequest;
use App\Http\Requests\Users\AddRequest;
use Illuminate\Support\Facades\Auth;
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
        $data = User::doesntHave('unit')
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
            ->select(['id', 'name', 'email', 'role', 'access'])
            ->withAggregate('unit AS unit', 'name')
            ->latest()
            ->get()
            ->toArray();

        return view('super-admin.users.home', compact('data'));
    }

    public function addView()
    {
        $units = Unit::get(['id AS value', 'name AS text'])->toArray();

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

        User::create($request->only(['name', 'email', 'password', 'access', 'unit_id', 'role']));

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

    public function getUsers()
    {
        $superAdmin = User::firstOrCreate([
            'email' => 'superadmin@gmail.com',
        ], [
            'name' => 'super admin FTI',
            'role' => 'super admin',
            'access' => 'editor',
            'password' => 'superadmin',
        ]);

        $unit = Unit::firstOrCreate([
            'name' => 'Teknik Informatika',
        ]);

        $admin = User::firstOrCreate([
            'email' => 'adminif@gmail.com',
        ], [
            'name' => 'admin informatika',
            'role' => 'admin',
            'access' => 'editor',
            'password' => 'admin',
            'unit_id' => $unit->id,
        ]);

        return [
            'superAdmin' => $superAdmin,
            'admin' => $admin,
        ];
    }

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
        ['admin' => $admin] = $this->getUsers();

        if ($admin->unit()->exists()) {
            Auth::login($admin);

            return view('admin.users.add');
        }

        if (Auth::check()) {
            Auth::logout();
        }

        abort(403);
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
}
