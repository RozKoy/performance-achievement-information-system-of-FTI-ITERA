<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Requests\Authentication\ForgetPasswordRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Mail\ForgetPasswordEmailSender;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class ForgetPasswordController extends Controller
{
    /**
     * @return Factory|View
     */
    public function view(): Factory|View
    {
        return view('authentication.forget-password');
    }

    /**
     * @param \App\Http\Requests\Authentication\ForgetPasswordRequest $request
     * @return RedirectResponse
     */
    public function action(ForgetPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request['email'])->first();

        if ($user) {
            DB::beginTransaction();

            try {
                $user->update(['token' => uuid_create()]);

                Mail::to($user->email)->send(new ForgetPasswordEmailSender($user->only([
                    'email',
                    'token',
                    'name',
                ])));

                DB::commit();

                return _ControllerHelpers::RedirectWithRoute('login')
                    ->with('success', 'Berhasil mengirim link ubah kata sandi, silahkan cek email anda');
            } catch (\Exception $e) {
                DB::rollBack();

                return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Alamat email tidak aktif']);
            }
        }

        return _ControllerHelpers::BackWithInputWithErrors(['email' => 'Alamat email tidak valid']);
    }
}
