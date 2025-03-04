<?php

namespace App\Http\Controllers\SuperAdmin\User;

use App\Http\Controllers\_ControllerHelpers;
use App\Http\Requests\Users\EditRequest;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\Unit;
use App\Models\User;

class UpdateUserSuperAdminController extends Controller
{
    /**
     * @param \App\Models\User $user
     * @return Factory|View
     */
    public function view(User $user): Factory|View
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
                ->map(function ($unit) use ($user): array {
                    $data = $unit->toArray();
                    if ($unit->value === $user->unit_id) {
                        $data['selected'] = true;
                    }
                    return $data;
                })
                ->toArray()
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
     * @param \App\Http\Requests\Users\EditRequest $request
     * @param \App\Models\User $user
     * @return RedirectResponse
     */
    public function action(EditRequest $request, User $user): RedirectResponse
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
}
