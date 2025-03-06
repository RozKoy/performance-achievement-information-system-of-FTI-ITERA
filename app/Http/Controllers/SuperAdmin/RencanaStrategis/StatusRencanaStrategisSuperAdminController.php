<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use App\Models\RSPeriod;
use App\Models\RSYear;

class StatusRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @param \App\Models\RSPeriod $period
     * @return RedirectResponse
     */
    public function action(RSPeriod $period): RedirectResponse
    {
        if ($period->status) {
            $period->status = false;
            $period->deadline()->dissociate();
        } else {
            $currentMonth = (int) Carbon::now()->format('m');
            $currentPeriod = $currentMonth <= 6 ? '1' : '2';

            $currentYearInstance = RSYear::currentTime();
            $currentPeriodInstance = RSPeriod::whereBelongsTo($currentYearInstance, 'year')
                ->where('period', $currentPeriod)
                ->firstOrFail();

            $period->deadline()->associate($currentPeriodInstance);
            $period->status = true;
        }
        $period->save();

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbaharui status rencana strategis');
    }
}
