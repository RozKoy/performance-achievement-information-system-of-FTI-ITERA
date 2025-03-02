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
use Illuminate\Support\Str;
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
            ->where(function (Builder $query) use ($request): void {
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
            ->orWhereHas('unit', function (Builder $query) use ($request): void {
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
        $request['password'] = Str::replace(" ", "_", $request['name']);

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
            ->map(function ($unit) use ($user): array {
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
}
