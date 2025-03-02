<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Contracts\View\View;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        return view('authentication.login');
    }

    /**
     * @param \App\Http\Requests\Authentication\LoginRequest $request
     * @return RedirectResponse
     */
    public function action(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request['email'])->first();

        if ($user) {
            if ($user->role === 'admin' && $user->unit()->doesntExist()) {
                return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Akun tidak valid']);
            }

            if (Auth::attempt($request->safe()->toArray())) {
                if ($user->token) {
                    $user->update(['token' => null]);
                }

                $route = $user->role === 'super admin' ? 'super-admin-dashboard' : 'admin-rs';

                return _ControllerHelpers::RedirectWithRoute($route)->with('success', 'Berhasil masuk. Selamat datang ~ SICAKI');
            }
        }

        return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Email atau kata sandi tidak benar']);
    }
}
