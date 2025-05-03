<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\ValidationRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\IKUAchievement;

class ValidationIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\ValidationRequest $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function action(ValidationRequest $request, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        [
            'data' => $data,
        ] = $request;

        foreach ($data ?? [] as $id => $item) {
            if ($achievement = IKUAchievement::whereKey($id)->whereBelongsTo($ikp)->first()) {
                try {
                    $achievement->note = $item['note'];
                } catch (\Exception $e) {
                }
                if (isset($item['status'])) {
                    $achievement->status = !$achievement->status;
                }
                $achievement->save();
            }
        }

        $evaluation = $ikp->evaluation;

        if ($evaluation) {
            $all = $ikp->achievements()->where('status', true)->count();

            $evaluation->status = $all >= $evaluation->target;
            $evaluation->save();
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil melakukan validasi data indikator kinerja utama');
    }
}
