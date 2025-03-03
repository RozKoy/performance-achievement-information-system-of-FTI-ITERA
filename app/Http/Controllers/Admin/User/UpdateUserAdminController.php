<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Requests\Users\EditAdminRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class UpdateUserAdminController extends Controller
{
    /**
     * @param string $id
     * @return Factory|View
     */
    public function view(string $id): Factory|View
    {
        $user = auth()->user();

        $data = User::whereKey($id)
            ->whereBelongsTo($user->unit, 'unit')
            ->firstOrFail();

        return view('admin.users.edit', compact([
            'data',
            'user',
        ]));
    }

    /**
     * @param \App\Http\Requests\Users\EditAdminRequest $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function action(EditAdminRequest $request, string $id): RedirectResponse
    {
        [
            'access' => $newAccess,
            'email' => $newEmail,
            'name' => $newName,
        ] = $request;

        $user = auth()->user();

        $data = User::whereKey($id)
            ->whereBelongsTo($user->unit, 'unit')
            ->firstOrFail();

        if ($data->email !== $newEmail) {
            $data->email = $newEmail;
        }

        if ($data->name !== $newName) {
            $data->name = $newName;
        }

        if ($data->access !== $newAccess) {
            $data->access = $newAccess;
        }

        DB::beginTransaction();

        try {
            $data->save();

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('admin-users')->with('success', 'Berhasil memperbarui data pengguna');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
