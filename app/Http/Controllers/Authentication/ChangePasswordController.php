<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\ChangePasswordRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\User;

class ChangePasswordController extends Controller
{
    /**
     * @param string $token
     * @return Factory|View
     */
    public function view(string $token): Factory|View
    {
        $user = User::where('token', $token)->firstOrFail();

        return view('authentication.change-password', compact('user'));
    }

    /**
     * @param \App\Http\Requests\Authentication\ChangePasswordRequest $request
     * @param string $token
     * @return RedirectResponse
     */
    public function action(ChangePasswordRequest $request, string $token): RedirectResponse
    {
        [
            'password' => $password,
        ] = $request;

        $user = User::where('token', $token)->firstOrFail();

        $user->update([
            'password' => $password,
            'token' => null,
        ]);

        return _ControllerHelpers::RedirectWithRoute('login')->with('success', 'Berhasil merubah kata sandi');
    }
}
