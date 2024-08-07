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
            ->firstOrFail();

        if ($user->role === 'admin' && $user->unit()->doesntExist()) {
            return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Email tidak dapat ditemukan']);
        }

        if (Auth::attempt($request->safe()->toArray())) {
            if ($user->token) {
                $user->update(['token' => null]);
            }

            if ($user->role === 'super admin') {
                return _ControllerHelpers::RedirectWithRoute('super-admin-dashboard');
            }

            return _ControllerHelpers::RedirectWithRoute('admin-dashboard');
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
            ->firstOrFail();

        try {
            $user->update(['token' => uuid_create()]);

            Mail::to($user->email)->send(new ForgetPasswordEmailSender($user->only([
                'email',
                'token',
                'name',
            ])));
        } catch (\Exception $e) {
            $user->update(['token' => null]);

            return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Alamat email tidak aktif']);
        }

        return _ControllerHelpers::RedirectWithRoute('login');
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

        return _ControllerHelpers::RedirectWithRoute('login');
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

        return _ControllerHelpers::RedirectWithRoute('login');
    }
}
