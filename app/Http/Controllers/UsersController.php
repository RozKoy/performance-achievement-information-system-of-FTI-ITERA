<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\EditAdminRequest;
use App\Http\Requests\Users\AddAdminRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Users\EditRequest;
use App\Http\Requests\Users\AddRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
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

    /**
     * User home view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request): Factory|View
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

    /**
     * User add view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addView(): Factory|View
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

    /**
     * User add function
     * @param \App\Http\Requests\Users\AddRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add(AddRequest $request): RedirectResponse
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

        return _ControllerHelpers::RedirectWithRoute('super-admin-users');
    }

    /**
     * User edit view
     * @param \App\Models\User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editView(User $user): Factory|View
    {
        $units = Unit::select([
            'name AS text',
            'id AS value',
        ])
            ->get()
            ->map(function ($unit) use ($user) {
                $data = $unit->toArray();
                if ($unit->value === $user->unit_id) {
                    $data['selected'] = true;
                }
                return $data;
            });

        $data = [
            [
                'value' => '',
                'text' => 'Pilih Unit'
            ],
            ...$units->toArray()
        ];

        $user = $user->only([
            'access',
            'email',
            'name',
            'role',
            'id',
        ]);

        return view('super-admin.users.edit', compact([
            'data',
            'user',
        ]));
    }

    /**
     * User edit function
     * @param \App\Http\Requests\Users\EditRequest $request
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function edit(EditRequest $request, User $user): RedirectResponse
    {
        $newEmail = $request['email'];

        if ($user->email !== $newEmail) {
            $user->email = $newEmail;
        }

        $newName = $request['name'];

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

        return _ControllerHelpers::RedirectWithRoute('super-admin-users');
    }

    /**
     * User delete function
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(User $user): RedirectResponse
    {
        $user->forceDelete();

        return back();
    }


    /*
    | -----------------------------------------------------------------
    | ADMIN
    | -----------------------------------------------------------------
    */

    /**
     * User home view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeViewAdmin(Request $request): Factory|View
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
            ->select([
                'access',
                'email',
                'name',
                'id',
            ])
            ->latest()
            ->get()
            ->toArray();

        return view('admin.users.home', compact('data'));
    }

    /**
     * User add view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function addViewAdmin(): Factory|View
    {
        return view('admin.users.add');
    }

    /**
     * User add function
     * @param \App\Http\Requests\Users\AddAdminRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addAdmin(AddAdminRequest $request): RedirectResponse
    {
        User::create(
            [
                ...$request->safe()->all(),
                'password' => str_replace(" ", "_", $request['name']),
                'unit_id' => auth()->user()->unit_id,
                'role' => 'admin'
            ]
        );

        return _ControllerHelpers::RedirectWithRoute('admin-users')
            ->with('success', 'Berhasil menambahkan pengguna');
    }

    /**
     * User edit view
     * @param string $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function editViewAdmin(string $id): Factory|View
    {
        $user = auth()->user()
            ->unit
            ->users()
            ->findOrFail($id);

        $user = $user->only([
            'access',
            'email',
            'name',
            'id',
        ]);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * User edit function
     * @param \App\Http\Requests\Users\EditAdminRequest $request
     * @param \App\Models\User $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function editAdmin(EditAdminRequest $request, User $id): RedirectResponse
    {
        $user = auth()->user()
            ->unit
            ->users()
            ->findOrFail($id->id);

        $newEmail = $request['email'];

        if ($user->email !== $newEmail) {
            $user->email = $newEmail;
        }

        $newName = $request['name'];

        if ($user->name !== $newName) {
            $user->name = $newName;
        }

        $newAccess = $request['access'];

        if ($user->access !== $newAccess) {
            $user->access = $newAccess;
        }

        $user->save();

        return _ControllerHelpers::RedirectWithRoute('admin-users')
            ->with('success', 'Berhasil memperbarui data pengguna');
    }

    /**
     * User delete function
     * @param \App\Models\User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAdmin(User $user): RedirectResponse
    {
        $user = auth()->user()
            ->unit
            ->users()
            ->findOrFail($user->id);

        $user->forceDelete();

        return _ControllerHelpers::Back()
            ->with('success', 'Berhasil menghapus data pengguna');
    }
}
