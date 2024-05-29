<?php

namespace App\Http\Controllers;

use App\Http\Requests\Authentication\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    public function loginView()
    {
        return view('authentication.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request['email'])
            ->firstOrFail();

        if ($user->role === 'admin' && $user->unit()->doesntExist()) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'Email tidak dapat ditemukan']);
        }

        if (Auth::attempt($request->safe()->toArray())) {
            if ($user->role === 'super admin') {
                return redirect()->route('super-admin-dashboard');
            }

            return redirect()->route('admin-dashboard');
        }

        return back()
            ->withInput()
            ->withErrors(['email' => 'Email atau kata sandi tidak benar']);
    }

    public function logout()
    {
        if (auth()->check()) {
            auth()->logout();
        }

        return redirect()->route('login');
    }
}
