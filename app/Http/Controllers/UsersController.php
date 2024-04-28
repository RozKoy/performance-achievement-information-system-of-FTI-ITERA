<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\AddRequest;
use App\Models\Unit;
use App\Models\User;

class UsersController extends Controller
{
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
}
