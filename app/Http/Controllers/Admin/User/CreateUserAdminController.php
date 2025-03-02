<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Requests\Users\AddAdminRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\User;

class CreateUserAdminController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        $user = auth()->user();

        return view('admin.users.add', compact([
            'user',
        ]));
    }

    /**
     * @param \App\Http\Requests\Users\AddAdminRequest $request
     * @return RedirectResponse
     */
    public function action(AddAdminRequest $request): RedirectResponse
    {
        [
            'name' => $name,
        ] = $request;

        $user = auth()->user();

        DB::beginTransaction();

        try {
            User::create([
                ...$request->safe()->all(),
                'password' => Str::replace(" ", "_", $name),
                'unit_id' => $user->unit_id,
                'role' => 'admin'
            ]);

            DB::commit();

            return _ControllerHelpers::RedirectWithRoute('admin-users')->with('success', 'Berhasil menambahkan pengguna');
        } catch (\Exception $e) {
            DB::rollBack();

            return _ControllerHelpers::BackWithInputWithErrors(['error' => $e->getMessage()]);
        }
    }
}
