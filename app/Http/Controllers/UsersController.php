<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Users\AddRequest;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;

class UsersController extends Controller
{
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

            if ($request['access'] === 'super-admin-editor') {
                $request['access'] = 'editor';
            } else {
                $request['access'] = 'viewer';
            }
        } else {
            $request['role'] = 'admin';

            if (!isset($request['access'])) {
                $request['access'] = 'editor';
            } else {
                $request['access'] = 'viewer';
            }

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
            ->first(['id', 'name', 'email', 'role', 'access', 'unit_id']);

        if ($user !== null) {
            $units = Unit::get(['id AS value', 'name AS text'])->toArray();
            $user = $user->toArray();

            $unit_id = $user['unit_id'];
            if ($unit_id !== null) {
                $units = array_map(function ($unit) use ($unit_id) {
                    if ($unit['value'] === $unit_id) {
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

            unset($user['unit_id']);

            return view('super-admin.users.edit', compact(['user', 'data']));
        }

        abort(404);
    }
}
