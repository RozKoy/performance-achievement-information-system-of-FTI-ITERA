<?php

namespace App\Http\Controllers\Utils;

use Illuminate\Http\RedirectResponse;

function BackWithInputWithErrors(string|array $errors): RedirectResponse
{
    return back()->withInput()->withErrors($errors);
}
