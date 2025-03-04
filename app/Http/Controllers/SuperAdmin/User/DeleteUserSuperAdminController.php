<?php

namespace App\Http\Controllers\SuperAdmin\User;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\User;

class DeleteUserSuperAdminController extends Controller
{
    /**
     * @param \App\Models\User $user
     * @return RedirectResponse
     */
    public function action(User $user): RedirectResponse
    {
        $user->forceDelete();

        return _ControllerHelpers::Back();
    }
}
