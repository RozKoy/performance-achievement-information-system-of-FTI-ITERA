<?php

namespace App\Http\Controllers\SuperAdmin\Unit;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\IKUYear;
use App\Models\RSYear;
use App\Models\Unit;
use App\Models\User;

class DeleteUnitSuperAdminController extends Controller
{
    /**
     * @param \App\Models\Unit $unit
     * @return RedirectResponse
     */
    public function action(Unit $unit): RedirectResponse
    {
        $ikuYear = IKUYear::currentTime();
        $rsYear = RSYear::currentTime();

        $unit->users()->each(function (User $user): void {
            $user->unit()->dissociate();
            $user->save();
        });

        $unit->rencanaStrategis()
            ->whereRelation('period', 'year_id', $rsYear->id)
            ->forceDelete();

        $oldRS = $unit->rencanaStrategis()
            ->whereRelation('period', 'year_id', '!=', $rsYear->id)
            ->exists();

        $unit->singleIndikatorKinerjaUtama()
            ->whereRelation('period', 'year_id', $ikuYear->id)
            ->forceDelete();

        $oldSingleIKU = $unit->singleIndikatorKinerjaUtama()
            ->whereRelation('period', 'year_id', '!=', $ikuYear->id)
            ->exists();

        $unit->indikatorKinerjaUtama()
            ->whereRelation('period', 'year_id', $ikuYear->id)
            ->each(function ($iku): void {
                $iku->deleteOrTrashed();
            });

        $oldIKU = $unit->indikatorKinerjaUtama()
            ->whereRelation('period', 'year_id', '!=', $ikuYear->id)
            ->exists();

        $unit->IKUStatus()
            ->whereRelation('period', 'year_id', $ikuYear->id)
            ->forceDelete();

        $oldStatusIKU = $unit->IKUStatus()
            ->whereRelation('period', 'year_id', '!=', $ikuYear->id)
            ->exists();

        $unit->rencanaStrategisTarget()
            ->whereHas('indikatorKinerja', function ($query) use ($rsYear): void {
                $query->whereHas('kegiatan', function ($query) use ($rsYear): void {
                    $query->whereRelation('sasaranStrategis', 'time_id', $rsYear->id);
                });
            })
            ->forceDelete();

        $unit->indikatorKinerjaUtamaTarget()
            ->whereHas('indikatorKinerjaProgram', function ($query) use ($ikuYear): void {
                $query->whereHas('programStrategis', function ($query) use ($ikuYear): void {
                    $query->whereHas('indikatorKinerjaKegiatan', function ($query) use ($ikuYear): void {
                        $query->whereRelation('sasaranKegiatan', 'time_id', $ikuYear->id);
                    });
                });
            })
            ->forceDelete();

        if (!$oldRS) {
            $unit->rencanaStrategisTarget()->forceDelete();
        }

        if (!$oldSingleIKU && !$oldIKU && !$oldStatusIKU) {
            $unit->indikatorKinerjaUtamaTarget()->forceDelete();
        }

        if (!$oldRS && !$oldSingleIKU && !$oldIKU && !$oldStatusIKU) {
            $unit->forceDelete();
        } else {
            $unit->delete();
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil menghapus unit');
    }
}
