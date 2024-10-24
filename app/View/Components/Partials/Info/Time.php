<?php

namespace App\View\Components\Partials\Info;

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\View\Component;
use DateInterval;

class Time extends Component
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
	public function render(): Factory|View
	{
		$date = $rsDeadline = $ikuDeadline = Carbon::now();

		$currentMonth = (int) $date->format('m');
		$currentYear = $date->format('Y');

		// Rencana Strategis
		$rsPeriods = ['Januari - Juni', 'Juli - Desember'];
		$rsIndex = $currentMonth <= 6 ? 0 : 1;

		// Indikator Kinerja Utama
		$ikuPeriods = ['TW 1 | Jan - Mar', 'TW 2 | Apr - Jun', 'TW 3 | Jul - Sep', 'TW 4 | Okt - Des'];
		$ikuPeriodList = [3, 6, 9, 12];
		$ikuIndex = 0;
		foreach ($ikuPeriodList as $key => $value) {
			if ($currentMonth <= $value) {
				$ikuIndex = $key;
				break;
			}
		}

		$iku = [$ikuPeriods[$ikuIndex], $currentYear];
		$rs = [$rsPeriods[$rsIndex], $currentYear];

		$ikuDeadline = $this->getDeadline($ikuDeadline, $ikuPeriodList[$ikuIndex]);
		$rsDeadline = $this->getDeadline($rsDeadline, $rsIndex ? 12 : 6);

		return view('components.partials.info.time', compact([
			'ikuDeadline',
			'rsDeadline',
			'iku',
			'rs',
		]));
	}

	public function getDeadline(Carbon $date, int $month): DateInterval
	{
		$date->setMonth($month);
		$date->setDay($date->format('t'));
		$date->setTime(23, 59, 59, 999999);

		return $date->diff(Carbon::now());
	}
}
