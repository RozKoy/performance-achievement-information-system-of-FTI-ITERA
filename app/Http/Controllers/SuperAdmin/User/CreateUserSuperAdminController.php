<?php

namespace App\Http\Controllers\SuperAdmin\User;

use App\Http\Controllers\_ControllerHelpers;
use App\Http\Requests\Users\AddRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Unit;
use App\Models\User;

class CreateUserSuperAdminController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        $data = [
            [
                'value' => '',
                'text' => 'Pilih Unit'
            ],
            ...Unit::select([
                'name AS text',
                'id AS value',
            ])
                ->get()
                ->toArray()
        ];

        return view('super-admin.users.add', compact('data'));
    }

    /**
     * @param \App\Http\Requests\Users\AddRequest $request
     * @return RedirectResponse
     */
    public function action(AddRequest $request): RedirectResponse
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

        DB::beginTransaction();

        try {
            User::create($request->only([
                'password',
                'access',
                'email',
                'name',
                'role',

                'unit_id',
            ]));

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('super-admin-users')->with('success', 'Berhasil menambahkan pengguna');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
