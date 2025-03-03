<?php

namespace App\Http\Controllers\Admin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\AddSingleDataRequest;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;

class AddSingleDataIndikatorKinerjaUtamaAdminController extends Controller
{
    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\AddSingleDataRequest $request
     * @param string $period
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function action(AddSingleDataRequest $request, string $period, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        if ($ikp->status !== 'aktif' || $ikp->mode !== 'single') {
            abort(404);
        }

        $user = auth()->user();

        [
            'value' => $valueRequest,
            'link' => $linkRequest,
        ] = $request;

        if ($valueRequest && !$linkRequest) {
            return _ControllerHelpers::BackWithInputWithErrors(['link' => 'Link bukti wajib diisi']);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentDate = Carbon::now();

        $periodInstance = $year->periods()
            ->whereDate('deadline', '>=', $currentDate)
            ->where('period', $period)
            ->where('status', true)
            ->firstOrFail();

        if ($valueRequest && $linkRequest) {
            $achievement = $ikp->singleAchievements()->firstOrNew(
                [
                    'indikator_kinerja_program_id' => $ikp->id,
                    'period_id' => $periodInstance->id,
                    'unit_id' => $user->unit->id,
                ],
                [
                    'indikator_kinerja_program_id' => $ikp->id,
                    'period_id' => $periodInstance->id,
                    'unit_id' => $user->unit->id,
                ],
            );

            if (!ctype_digit($valueRequest)) {
                $valueRequest = number_format((float) $valueRequest, 2);
            }

            $achievement->value = $valueRequest;
            $achievement->link = $linkRequest;

            $achievement->save();
        } else {
            $ikp->singleAchievements()
                ->whereBelongsTo($periodInstance, 'period')
                ->whereBelongsTo($user->unit)
                ->forceDelete();
        }

        $evaluation = $ikp->evaluation;

        if ($evaluation) {
            $value = $ikp->singleAchievements()->average('value');

            $status = $value >= $evaluation->target;

            $evaluation->status = $status;
            $evaluation->save();
        }

        $ikp->unitStatus()
            ->whereBelongsTo($periodInstance, 'period')
            ->whereBelongsTo($user->unit, 'unit')
            ->forceDelete();

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbarui data');
    }
}
