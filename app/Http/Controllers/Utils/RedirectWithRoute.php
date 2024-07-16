<?php

namespace App\Http\Controllers\Utils;

use Illuminate\Http\RedirectResponse;

function RedirectWithRoute(string $route): RedirectResponse
{
    return redirect()->route($route);
}
