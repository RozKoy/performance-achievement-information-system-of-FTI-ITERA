<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\ChangePasswordRequest;
use App\Http\Requests\Authentication\ForgetPasswordRequest;
use App\Http\Requests\Authentication\LoginRequest;
use App\Mail\ForgetPasswordEmailSender;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Contracts\View\View;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Login view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function loginView(): Factory|View
    {
        return view('authentication.login');
    }

    /**
     * Forget password view
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function forgetPasswordView(): Factory|View
    {
        return view('authentication.forget-password');
    }

    /**
     * Change password view
     * @param string $token
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function changePasswordView(string $token): Factory|View
    {
        $user = User::where('token', $token)
            ->firstOrFail();

        $user->only('email');

        return view('authentication.change-password', compact('user'));
    }

    /**
     * Login
     * @param \App\Http\Requests\Authentication\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request['email'])
            ->first();

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

    /**
     * Forget password
     * @param \App\Http\Requests\Authentication\ForgetPasswordRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forgetPassword(ForgetPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request['email'])
            ->first();

        if ($user) {
            try {
                $user->update(['token' => uuid_create()]);

                Mail::to($user->email)->send(new ForgetPasswordEmailSender($user->only([
                    'email',
                    'token',
                    'name',
                ])));

                return _ControllerHelpers::RedirectWithRoute('login')
                    ->with('success', 'Berhasil mengirim link ubah kata sandi, silahkan cek email anda');
            } catch (\Exception $e) {
                $user->update(['token' => null]);

                return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Alamat email tidak aktif']);
            }
        }

        return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Alamat email tidak valid']);
    }

    /**
     * Change password
     * @param \App\Http\Requests\Authentication\ChangePasswordRequest $request
     * @param string $token
     * @return \Illuminate\Http\RedirectResponse
     */
    public function changePassword(ChangePasswordRequest $request, string $token): RedirectResponse
    {
        $user = User::where('token', $token)
            ->firstOrFail();

        $user->update([
            'password' => $request['password'],
            'token' => null
        ]);

        return _ControllerHelpers::RedirectWithRoute('login')
            ->with('success', 'Berhasil merubah kata sandi');
    }

    /**
     * Logout
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        if (auth()->check()) {
            auth()->logout();
        }

        return _ControllerHelpers::RedirectWithRoute('login')
            ->with('success', 'Berhasil keluar');
    }
}
