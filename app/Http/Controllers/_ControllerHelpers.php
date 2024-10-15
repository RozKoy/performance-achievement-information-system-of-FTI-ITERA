<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class _ControllerHelpers
{
    /**
     * BackWithInputWithErrors
     * @param string|array $errors
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function BackWithInputWithErrors(string|array $errors): RedirectResponse
    {
        return back()->withInput()->withErrors($errors);
    }

    /**
     * RedirectWithRoute
     * @param string $route
     * @return \Illuminate\Http\RedirectResponse
     */
    public static function RedirectWithRoute(string $route, array $parameters = []): RedirectResponse
    {
        return redirect()->route($route, $parameters);
    }
}
