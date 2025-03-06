<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use App\Http\Requests\RencanaStrategis\AddEvaluationRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\IndikatorKinerja;

class EvaluationRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @param \App\Http\Requests\RencanaStrategis\AddEvaluationRequest $request
     * @param \App\Models\IndikatorKinerja $ik
     * @return RedirectResponse
     */
    public function action(AddEvaluationRequest $request, IndikatorKinerja $ik): RedirectResponse
    {
        if ($request['period'] === '3') {
            if ($request['target'] === null && $ik->status !== 'aktif') {
                return _ControllerHelpers::BackWithInputWithErrors(['target' => 'Target wajib diisi']);
            } else if ($ik->type !== IndikatorKinerja::TYPE_TEXT && $ik->status !== 'aktif' && !is_numeric($request['target'])) {
                return _ControllerHelpers::BackWithInputWithErrors(['target' => 'Target harus berupa angka']);
            }

            if ($ik->type === IndikatorKinerja::TYPE_TEXT && $request['status'] === null) {
                return _ControllerHelpers::BackWithInputWithErrors(['status' => 'Status wajib diisi']);
            }
        } else if ($ik->status === 'tidak aktif' && $ik->type !== IndikatorKinerja::TYPE_TEXT && $request['realization'] !== null) {
            if (!is_numeric($request['realization'])) {
                return _ControllerHelpers::BackWithInputWithErrors(['realization' => 'Realisasi harus berupa angka']);
            } else if ((float) $request['realization'] < 0) {
                $request['realization'] *= -1;
                $request['realization'] = "{$request['realization']}";
            }
        }

        if ($ik->type === IndikatorKinerja::TYPE_TEXT) {
            if ($request['realization']) {
                if (!$ik->textSelections->firstWhere('id', $request['realization'])) {
                    return _ControllerHelpers::BackWithInputWithErrors(['realization' => 'Realisasi tidak valid']);
                }
            }
            if ($request['target']) {
                if (!$ik->textSelections->firstWhere('id', $request['target'])) {
                    return _ControllerHelpers::BackWithInputWithErrors(['target' => 'Target tidak valid']);
                }
            }
        }

        $k = $ik->kegiatan;
        $ss = $k->sasaranStrategis;

        $yearInstance = $ss->time;
        $periodInstance = $yearInstance->periods()
            ->where('period', $request['period'])
            ->first();

        if ($ik->type === IndikatorKinerja::TYPE_TEXT || ($ik->type !== IndikatorKinerja::TYPE_TEXT && $ik->status === 'tidak aktif' && $periodInstance !== null)) {
            $realization = $ik->realization()
                ->firstOrNew([
                    'period_id' => $periodInstance?->id,
                    'unit_id' => null,
                ]);

            if ($request['realization'] !== null) {
                $realization->realization = $request['realization'];
                $realization->save();
            } else if ($realization->id !== null) {
                $realization->forceDelete();
            }

            if ($ik->type !== IndikatorKinerja::TYPE_TEXT) {
                $all = $ik->realization()
                    ->whereNotNull('period_id')
                    ->whereNull('unit_id')
                    ->get();

                $temp = $ik->realization()
                    ->firstOrNew([
                        'period_id' => null,
                        'unit_id' => null,
                    ]);

                $final = $all->sum('realization');
                if ($ik->type === IndikatorKinerja::TYPE_PERCENT) {
                    $final = $all->average('realization');
                    if (!ctype_digit(text: (string) $final)) {
                        $final = number_format((float) $final, 2);
                    }
                }

                $temp->realization = $final;
                $temp->save();
            }
        }

        if ($request['period'] === '3') {
            $evaluation = $ik->evaluation()->firstOrNew();

            $target = 0;
            if ($ik->type !== IndikatorKinerja::TYPE_TEXT && $ik->status === 'aktif') {
                $target = $evaluation->target !== null ? $evaluation->target : 0;
            } else {
                $target = $request['target'];
                if ($target === null) {
                    $target = $evaluation->target !== null ? $evaluation->target : 0;
                }
            }

            $evaluation->status = $request['status'] !== null ? $request['status'] : false;
            $evaluation->evaluation = $request['evaluation'];
            $evaluation->follow_up = $request['follow_up'];
            $evaluation->target = $target;

            $evaluation->save();
        }

        if ($ik->type !== IndikatorKinerja::TYPE_TEXT) {
            $evaluation = $ik->evaluation;

            if ($evaluation) {
                $realization = $ik->realization()
                    ->whereNull(['period_id', 'unit_id'])
                    ->first();

                $evaluation->status = false;
                if ($realization) {
                    $evaluation->status = (float) $realization->realization >= (float) $evaluation->target;
                }

                $evaluation->save();
            }
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil menambahkan evaluasi rencana strategis');
    }
}
