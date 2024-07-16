<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class _ControllerHelpers
{
    public static function BackWithInputWithErrors(string|array $errors): RedirectResponse
    {
        return back()->withInput()->withErrors($errors);
    }

    public static function RedirectWithRoute(string $route): RedirectResponse
    {
        return redirect()->route($route);
    }
}
