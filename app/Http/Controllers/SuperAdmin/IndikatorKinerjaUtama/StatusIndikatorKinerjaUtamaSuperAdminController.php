<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\SetDeadlineRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Models\IKUPeriod;

class StatusIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @param \App\Models\IKUPeriod $period
     * @return RedirectResponse
     */
    public function action(IKUPeriod $period): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $deadline = null;
        $status = false;

        if (!$period->status) {
            $deadline = Carbon::now();
            $currentMonth = (int) $deadline->format('m');

            foreach ([3, 6, 9, 12] as $key => $value) {
                if ($currentMonth <= $value) {
                    $deadline->setMonth($value);
                    break;
                }
            }

            $deadline->setDay($deadline->daysInMonth);
            $status = true;
        }

        $period->update([
            'deadline' => $deadline,
            'status' => $status,
        ]);

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbaharui status indikator kinerja utama');
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\SetDeadlineRequest $request
     * @param \App\Models\IKUPeriod $period
     * @return RedirectResponse
     */
    public function setDeadline(SetDeadlineRequest $request, IKUPeriod $period): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        [
            "$period->id-deadline" => $deadline,
        ] = $request;

        if ($period->status) {
            $period->update([
                'deadline' => $deadline,
            ]);
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil menentukan batas waktu indikator kinerja utama');
    }
}
