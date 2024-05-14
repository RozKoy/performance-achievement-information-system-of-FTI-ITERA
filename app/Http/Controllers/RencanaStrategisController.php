<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSPeriod;
use App\Models\RSYear;

class RencanaStrategisController extends Controller
{
    public function homeView(Request $request)
    {
        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        $year = isset($request->year) ? $request->year : $currentYear;

        if ($year === $currentYear) {
            $year = RSYear::currentTime();
        } else {
            $year = RSYear::where('year', $year)->firstOrFail();
        }

        $periodList = ['1'];
        if ($year->year !== $currentYear || $currentMonth >= 6) {
            $periodList[] = '2';
        }

        foreach ($periodList as $key => $value) {
            $temp = RSPeriod::firstOrNew([
                'year_id' => $year->id,
                'period' => $value,
            ], [
                'status' => true,
            ]);

            if ($temp->id === null) {
                $temp->save();

                $temp->deadline_id = $temp->id;
                $temp->save();
            }
        }

        return view('super-admin.achievement.rs.home');
    }
}
