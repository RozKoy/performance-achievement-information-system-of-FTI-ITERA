<?php

namespace App\Http\Controllers;

use App\Http\Requests\Units\AddRequest;
use App\Models\Unit;

class UnitsController extends Controller
{
    public function add(AddRequest $request)
    {
        $unit = Unit::create($request->safe()->only('name'));

        return redirect()->route('super-admin-unit');
    }
}
