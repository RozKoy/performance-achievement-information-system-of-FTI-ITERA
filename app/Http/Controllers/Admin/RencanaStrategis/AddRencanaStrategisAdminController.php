<?php

namespace App\Http\Controllers\Admin\RencanaStrategis;

use App\Http\Requests\RencanaStrategis\AddRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Carbon;
use App\Models\RSAchievement;
use App\Models\RSPeriod;

class AddRencanaStrategisAdminController extends Controller
{
    /**
     * @param \App\Http\Requests\RencanaStrategis\AddRequest $request
     * @param string $periodId
     * @param string $ikId
     * @return RedirectResponse
     */
    public function action(AddRequest $request, string $periodId, string $ikId): RedirectResponse
    {
        [
            "realization-$ikId" => $realization,
            "link-$ikId" => $link,
        ] = $request;

        $user = auth()->user();

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = $currentDate->format('Y');

        $period = RSPeriod::whereKey($periodId)
            ->where('status', true)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear): void {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear): void {
                        $query->where('year', $currentYear);
                    });
            })
            ->firstOrFail();

        $ik = IndikatorKinerja::whereKey($ikId)
            ->where('status', 'aktif')
            ->whereHas('kegiatan', function (Builder $query) use ($period): void {
                $query->whereHas('sasaranStrategis', function (Builder $query) use ($period): void {
                    $query->whereBelongsTo($period->year, 'time');
                });
            })
            ->firstOrFail();


        if ($realization !== null && $link === null) {
            return _ControllerHelpers::BackWithInputWithErrors(["link-$ikId" => 'Link bukti wajib diisi']);
        }

        if ($realization !== null && !is_numeric($realization) && ($ik->type === 'persen' || $ik->type === 'angka')) {
            return _ControllerHelpers::BackWithInputWithErrors(["realization-$ikId" => 'Realisasi tidak sesuai dengan tipe data']);
        }

        if ($ik->type !== 'teks' && $realization !== null) {
            $realization = (float) $realization;
            if ($realization < 0) {
                $realization *= -1;
            }
            if (!ctype_digit((string) $realization)) {
                $realization = number_format($realization, 2);
            }
        } else if ($ik->type === 'teks' && $realization !== null) {
            $selectionExists = $ik->textSelections()->find($realization);
            if ($selectionExists === null) {
                return _ControllerHelpers::BackWithInputWithErrors(["realization-$ikId" => 'Pilihan tidak dapat ditemukan']);
            }
        }

        $allAchievement = RSAchievement::firstOrNew([
            'indikator_kinerja_id' => $ik->id,
            'period_id' => null,
            'unit_id' => null,
        ]);

        $periodAchievement = RSAchievement::firstOrNew([
            'indikator_kinerja_id' => $ik->id,
            'period_id' => $period->id,
            'unit_id' => null,
        ]);

        $unitAchievement = RSAchievement::firstOrNew([
            'unit_id' => $user->unit->id,
            'indikator_kinerja_id' => $ik->id,
            'period_id' => null,
        ]);

        $achievement = RSAchievement::whereBelongsTo($user->unit)
            ->whereBelongsTo($period, 'period')
            ->whereBelongsTo($ik);

        if ($realization !== null) {
            $achievement = $achievement->firstOrNew();

            $achievement->realization = (string) $realization;
            $achievement->link = (string) $link;

            $achievement->unit()->associate($user->unit);
            $achievement->indikatorKinerja()->associate($ik);
            $achievement->period()->associate($period);

            $achievement->save();
        } else {
            $achievement->forceDelete();
        }

        if ($ik->type !== 'teks') {
            foreach ([$allAchievement, $periodAchievement, $unitAchievement] as $instance) {
                $all = RSAchievement::whereBelongsTo($ik)
                    ->where(function (Builder $query) use ($instance, $user): void {
                        if ($instance->period) {
                            $query->whereBelongsTo($instance->period, 'period')
                                ->whereNotNull('unit_id');
                        } else if ($instance->unit) {
                            $query->whereBelongsTo($user->unit)
                                ->whereNotNull('period_id');
                        } else {
                            $query->whereNotNull('period_id')
                                ->whereNotNull('unit_id');
                        }
                    })
                    ->get();

                $sum = $ik->type === 'angka' ? $all->sum('realization') : $all->average('realization');

                if (!ctype_digit((string) $sum)) {
                    $sum = number_format($sum, 2);
                }

                $instance->realization = (string) $sum;
                $instance->save();
            }
        }

        $evaluation = $ik->evaluation;

        if ($evaluation) {
            if ($ik->type === 'teks') {
                $unexpectedCount = RSAchievement::whereNotNull('unit_id')
                    ->whereBelongsTo($period, 'period')
                    ->whereBelongsTo($ik)
                    ->whereNot('realization', $evaluation->target)
                    ->count();

                $evaluation->status = $unexpectedCount === 0;
            } else {
                $evaluation->status = (float) $allAchievement->realization >= (float) $evaluation->target;
            }
            $evaluation->save();
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbaharui data');
    }
}
