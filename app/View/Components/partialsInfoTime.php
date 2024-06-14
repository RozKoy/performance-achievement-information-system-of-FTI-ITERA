<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;
use Closure;

class PartialsInfoTime extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        // Rencana Strategis
        $rsPeriods = ['Januari - Juni', 'Juli - Desember'];
        $rsIndex = $currentMonth <= 6 ? 0 : 1;

        // Indikator Kinerja Utama
        $ikuPeriods = ['TW 1 | Jan - Mar', 'TW 2 | Apr - Jun', 'TW 3 | Jul - Sep', 'TW 4 | Okt - Des'];
        $ikuIndex = 0;
        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $ikuIndex = $key;
                break;
            }
        }

        $iku = [$ikuPeriods[$ikuIndex], $currentYear];
        $rs = [$rsPeriods[$rsIndex], $currentYear];

        return view('components.partials.info.time', compact([
            'iku',
            'rs',
        ]));
    }
}
