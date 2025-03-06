<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\AddEvaluationRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class EvaluationIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\AddEvaluationRequest $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function action(AddEvaluationRequest $request, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $yearInstance = $sk->time;

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period');

        if ($periods->count() === 4) {
            $evaluation = $ikp->evaluation()
                ->firstOrNew([], [
                    'status' => false,
                    'target' => 0,
                ]);

            $evaluation->evaluation = $request['evaluation'];
            $evaluation->follow_up = $request['follow_up'];

            $achievementCount = $ikp->achievements->count();
            if ($achievementCount) {
                $evaluation->status = $achievementCount >= $evaluation->target;
            }

            $evaluation->save();

            return _ControllerHelpers::Back()->with('success', 'Berhasil memperbaharui evaluasi indikator kinerja utama');
        }

        abort(404);
    }
}
