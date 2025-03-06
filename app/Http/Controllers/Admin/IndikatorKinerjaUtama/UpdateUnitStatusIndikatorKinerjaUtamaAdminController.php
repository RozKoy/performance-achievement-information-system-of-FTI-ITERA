<?php

namespace App\Http\Controllers\Admin\IndikatorKinerjaUtama;

use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\HomeIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Controllers\_ControllerHelpers;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Models\IKUUnitStatus;

class UpdateUnitStatusIndikatorKinerjaUtamaAdminController extends Controller
{
    /**
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function yearStatusToggle(IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $user = auth()->user();

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $singleData = $ikp->singleAchievements()->whereBelongsTo($user->unit, 'unit')->get();
        $tableData = $ikp->achievements()->whereBelongsTo($user->unit, 'unit')->get();

        $check = ($ikp->mode === 'table' && $tableData->count() === 0) || ($ikp->mode === 'single' && $singleData->count() === 0);

        if ($ikp->status !== 'aktif' || !$check) {
            abort(404);
        }

        $unitStatus = $ikp->unitStatus()->whereBelongsTo($user->unit, 'unit');

        $unitStatus->forceDelete();

        if ($unitStatus->count() !== 4) {
            $currentDate = Carbon::now();

            $insertData = [];
            foreach ($year->periods as $period) {
                $insertData[] = [
                    'id' => uuid_create(),

                    'indikator_kinerja_program_id' => $ikp->id,
                    'unit_id' => $user->unit->id,
                    'period_id' => $period->id,

                    'status' => IKUUnitStatus::STATUS_BLANK,

                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ];
            }

            if (count($insertData)) {
                IKUUnitStatus::insert($insertData);
            }
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbarui status data tahunan');
    }

    /**
     * @param string $period
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function statusToggle(string $period, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $user = auth()->user();

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

        $singleData = $ikp->singleAchievements()->whereBelongsTo($user->unit, 'unit')
            ->whereBelongsTo($periodInstance, 'period')
            ->get();
        $tableData = $ikp->achievements()->whereBelongsTo($user->unit, 'unit')
            ->whereBelongsTo($periodInstance, 'period')
            ->get();

        $check = ($ikp->mode === 'table' && $tableData->count() === 0) || ($ikp->mode === 'single' && $singleData->count() === 0);

        if ($ikp->status !== 'aktif' || !$check) {
            abort(404);
        }

        $unitStatus = $ikp->unitStatus()->whereBelongsTo($user->unit, 'unit')
            ->whereBelongsTo($periodInstance, 'period');

        if ($unitStatus->exists()) {
            $unitStatus->forceDelete();
        } else {
            $ikp->unitStatus()->create([
                'period_id' => $periodInstance->id,
                'unit_id' => $user->unit->id,

                'status' => IKUUnitStatus::STATUS_BLANK,
            ]);
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbarui unit status');
    }
}
