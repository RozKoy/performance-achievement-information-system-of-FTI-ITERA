<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\User;

class DeleteUserAdminController extends Controller
{
    /**
     * @param \App\Models\User $user
     * @return RedirectResponse
     */
    public function action(User $user): RedirectResponse
    {
        if ($user->unit_id !== auth()->user()->unit_id) {
            abort(404);
        }

        $user->forceDelete();

        return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus data pengguna');
    }
}
