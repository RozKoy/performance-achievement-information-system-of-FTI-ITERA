<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class LogoutController extends Controller
{
    public function action(): RedirectResponse
    {
        if (auth()->check()) {
            auth()->logout();
        }

        return _ControllerHelpers::RedirectWithRoute('login')->with('success', 'Berhasil keluar');
    }
}
