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

use function App\Http\Controllers\Utils\BackWithInputWithErrors;
use function App\Http\Controllers\Utils\RedirectWithRoute;

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
     * Login
     * @param \App\Http\Requests\Authentication\LoginRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $user = User::where('email', $request['email'])
            ->firstOrFail();

        if ($user->role === 'admin' && $user->unit()->doesntExist()) {
            return BackWithInputWithErrors(['email' => 'Email tidak dapat ditemukan']);
        }

        if (Auth::attempt($request->safe()->toArray())) {
            if ($user->token) {
                $user->update(['token' => null]);
            }

            if ($user->role === 'super admin') {
                return RedirectWithRoute('super-admin-dashboard');
            }

            return RedirectWithRoute('admin-dashboard');
        }

        return BackWithInputWithErrors(['email' => 'Email atau kata sandi tidak benar']);
    }

    public function forgetPasswordView()
    {
        return view('authentication.forget-password');
    }

    public function forgetPassword(ForgetPasswordRequest $request)
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

            return back()
                ->withInput()
                ->withErrors(['email' => 'Alamat email tidak aktif']);
        }

        return redirect()->route('login');
    }

    public function changePasswordView($token)
    {
        $user = User::where('token', $token)->firstOrFail();
        $user->only('email');

        return view('authentication.change-password', compact('user'));
    }

    public function changePassword(ChangePasswordRequest $request, $token)
    {
        $user = User::where('token', $token)->firstOrFail();

        $user->update([
            'password' => $request['password'],
            'token' => null
        ]);

        return redirect()->route('login');
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->logout();
        }

        return redirect()->route('login');
    }
}
