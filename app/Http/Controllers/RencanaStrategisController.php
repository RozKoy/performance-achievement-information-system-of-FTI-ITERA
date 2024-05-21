<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Requests\RencanaStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Carbon;
use App\Models\RSAchievement;
use Illuminate\Http\Request;
use App\Models\RSPeriod;
use App\Models\RSYear;

class RencanaStrategisController extends Controller
{
    /*
    | -----------------------------------------------------------------
    | SUPER ADMIN
    | -----------------------------------------------------------------
    */

    public function homeView(Request $request)
    {
        if (isset($request->year)) {
            if (!is_numeric($request->year)) {
                abort(404);
            }
        }
        if (isset($request->period)) {
            if ($request->period !== '1' && $request->period !== '2' && $request->period !== '3') {
                abort(404);
            }
        }

        $currentYearInstance = RSYear::currentTime();

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $this->checkRoutine($currentYear);

        $years = RSYear::orderBy('year')->pluck('year')->toArray();

        if (count($years)) {
            $year = isset($request->year) ? $request->year : end($years);
            $yearInstance = RSYear::where('year', $year)->firstOrFail();

            $this->periodFirstOrNew($yearInstance->id, '1');
            if ($year !== $currentYear || $currentPeriod === '2') {
                $this->periodFirstOrNew($yearInstance->id, '2');
            }

            $temp = $yearInstance->periods()
                ->orderBy('period')
                ->pluck('period')
                ->flatten()
                ->unique()
                ->toArray();

            $periods = array_map(function ($item) {
                $title = 'Januari - Juni';
                if ($item === '2') {
                    $title = 'Juli - Desember';
                }
                return [
                    'title' => $title,
                    'value' => $item
                ];
            }, $temp);
            if (count($periods) === 2) {
                $periods[] = [
                    'title' => 'Januari - Desember',
                    'value' => '3',
                ];
            }
            $period = isset($request->period) ? $request->period : end($periods)['value'];

            if ((int) $period > count($periods)) {
                abort(404);
            }

            $system = true;
            $periodInstance = null;
            if ($period !== '3') {
                $periodInstance = RSPeriod::where('year_id', $yearInstance->id)
                    ->where('period', $period)
                    ->firstOrFail();

                $system = $periodInstance->status;
            }

            $data = $yearInstance->sasaranStrategis()
                ->whereHas('indikatorKinerja')
                ->with('kegiatan', function (HasMany $query) use ($periodInstance) {
                    $query->whereHas('indikatorKinerja')
                        ->orderBy('number')
                        ->select(['id', 'number', 'name AS k', 'sasaran_strategis_id'])
                        ->with('indikatorKinerja', function (HasMany $query) use ($periodInstance) {
                            $query->orderBy('number')
                                ->select(['id', 'type', 'number', 'status', 'name AS ik', 'kegiatan_id'])
                                ->withAggregate([
                                    'realization AS realization' => function (Builder $query) use ($periodInstance) {
                                        $query->whereNull('unit_id');
                                        if ($periodInstance) {
                                            $query->where('period_id', $periodInstance->id);
                                        } else {
                                            $query->whereNull('period_id');
                                        }
                                    }
                                ], 'realization')
                                ->withCount([
                                    'realization AS evaluation',
                                    'realization AS follow_up',
                                    'realization AS target',
                                    'realization AS count',
                                    'realization AS done',
                                ]);
                        })
                        ->withCount('indikatorKinerja AS rowspan');
                })
                ->orderBy('number')
                ->select(['id', 'number', 'name AS ss'])
                ->withCount('indikatorKinerja AS rowspan')
                ->get()
                ->toArray();

            $badge = [
                $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni'),
                $year
            ];
        } else {
            $system = false;

            $periods = [];

            $period = '';
            $year = '';

            $badge = [];
            $data = [];
        }

        return view('super-admin.achievement.rs.home', compact([
            'periods',
            'period',
            'system',
            'badge',
            'years',
            'year',
            'data',
        ]));
    }

    public function periodFirstOrNew($yearId, $value)
    {
        $temp = RSPeriod::firstOrNew([
            'year_id' => $yearId,
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

    public function checkRoutine($currentYear)
    {
        $years = RSYear::whereNot('year', $currentYear)
            ->where(function (Builder $query) {
                $query->doesntHave('periods')
                    ->orDoesntHave('sasaranStrategis')
                    ->orWhereHas('sasaranStrategis', function (Builder $query) {
                        $query->doesntHave('indikatorKinerja');
                    });
            })
            ->get();

        foreach ($years as $key => $year) {
            $year->periods()->update(['deadline_id' => null]);
            $sss = $year->sasaranStrategis;
            foreach ($sss as $key => $ss) {
                $ks = $ss->kegiatan;
                foreach ($ks as $key => $k) {
                    $iks = $k->indikatorKinerja;
                    foreach ($iks as $key => $ik) {
                        $ik->realization()->forceDelete();
                    }
                    $k->indikatorKinerja()->forceDelete();
                }
                $ss->kegiatan()->forceDelete();
            }
            $year->sasaranStrategis()->forceDelete();
            $year->periods()->forceDelete();
            $year->forceDelete();
        }
    }


    /*
    | -----------------------------------------------------------------
    | ADMIN
    | -----------------------------------------------------------------
    */

    public function homeViewAdmin(Request $request)
    {
        $status = [
            [
                'text' => 'Semua',
                'value' => '',
            ],
            [
                'text' => 'Belum diisi',
                'value' => 'undone',
            ],
            [
                'text' => 'Sudah diisi',
                'value' => 'done',
            ],
        ];

        if (isset($request->year)) {
            if (!is_numeric($request->year)) {
                abort(404);
            }
        }
        if (isset($request->period)) {
            if ($request->period !== '1' && $request->period !== '2') {
                abort(404);
            }
        }
        $statusIndex = 0;
        if ($request->status === 'undone') {
            $statusIndex = 1;
        } else if ($request->status === 'done') {
            $statusIndex = 2;
        }
        $status[$statusIndex] = [
            ...$status[$statusIndex],
            'selected' => true,
        ];

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $years = RSPeriod::where('status', true)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->withAggregate('year AS year', 'year')
            ->orderBy('year')
            ->get()
            ->pluck('year')
            ->flatten()
            ->unique()
            ->toArray();

        if (count($years)) {
            $year = isset($request->year) ? $request->year : end($years);
            $yearInstance = RSYear::where('year', $year)->firstOrFail();

            $temp = $yearInstance->periods()
                ->where('status', true)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->orderBy('period')
                ->pluck('period')
                ->flatten()
                ->unique()
                ->toArray();

            if (!count($temp)) {
                abort(404);
            }

            $periods = array_map(function ($item) {
                $title = 'Januari - Juni';
                if ($item === '2') {
                    $title = 'Juli - Desember';
                }
                return [
                    'title' => $title,
                    'value' => $item
                ];
            }, $temp);
            $period = isset($request->period) ? $request->period : end($periods)['value'];
            $periodInstance = RSPeriod::where('status', true)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->firstOrFail();

            $data = $yearInstance->sasaranStrategis()
                ->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex) {
                    $query->where('status', 'aktif');
                    if ($statusIndex === 1) {
                        $query->whereDoesntHave('realization', function (Builder $query) {
                            $query->whereBelongsTo(auth()->user()->unit);
                        });
                    } else if ($statusIndex === 2) {
                        $query->whereHas('realization', function (Builder $query) {
                            $query->whereBelongsTo(auth()->user()->unit);
                        });
                    }
                })
                ->with('kegiatan', function (HasMany $query) use ($statusIndex, $periodInstance) {
                    $query->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex) {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) {
                                $query->whereBelongsTo(auth()->user()->unit);
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) {
                                $query->whereBelongsTo(auth()->user()->unit);
                            });
                        }
                    })
                        ->orderBy('number')
                        ->select(['id', 'number', 'name AS k', 'sasaran_strategis_id'])
                        ->with('indikatorKinerja', function (HasMany $query) use ($statusIndex, $periodInstance) {
                            if ($statusIndex === 1) {
                                $query->whereDoesntHave('realization', function (Builder $query) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                });
                            } else if ($statusIndex === 2) {
                                $query->whereHas('realization', function (Builder $query) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                });
                            }
                            $query->where('status', 'aktif')
                                ->orderBy('number')
                                ->select(['id', 'type', 'number', 'name AS ik', 'kegiatan_id'])
                                ->withAggregate([
                                    'realization AS realization' => function (Builder $query) use ($periodInstance) {
                                        $query->whereBelongsTo(auth()->user()->unit)
                                            ->whereBelongsTo($periodInstance, 'period');
                                    }
                                ], 'realization');
                        })
                        ->withCount([
                            'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex) {
                                $query->where('status', 'aktif');
                                if ($statusIndex === 1) {
                                    $query->whereDoesntHave('realization', function (Builder $query) {
                                        $query->whereBelongsTo(auth()->user()->unit);
                                    });
                                } else if ($statusIndex === 2) {
                                    $query->whereHas('realization', function (Builder $query) {
                                        $query->whereBelongsTo(auth()->user()->unit);
                                    });
                                }
                            }
                        ]);
                })
                ->orderBy('number')
                ->select(['id', 'number', 'name AS ss'])
                ->withCount([
                    'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex) {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) {
                                $query->whereBelongsTo(auth()->user()->unit);
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) {
                                $query->whereBelongsTo(auth()->user()->unit);
                            });
                        }
                    }
                ])
                ->get()
                ->toArray();

            $allData = $yearInstance->sasaranStrategis()
                ->withCount([
                    'indikatorKinerja AS all' => function (Builder $query) {
                        $query->where('status', 'aktif');
                    },
                    'indikatorKinerja AS done' => function (Builder $query) {
                        $query->where('status', 'aktif')
                            ->whereHas('realization', function (Builder $query) {
                                $query->whereBelongsTo(auth()->user()->unit);
                            });
                    },
                ])
                ->get();

            $doneCount = $allData->sum('done');
            $allCount = $allData->sum('all');

            $badge = [
                $period === '1' ? 'Januari - Juni' : 'Juli - Desember',
                $year
            ];

            $periodId = $periodInstance->id;
        } else {
            $periods = [];

            $period = '';
            $year = '';

            $badge = [];
            $data = [];

            $periodId = '';

            $doneCount = 0;
            $allCount = 0;
        }

        return view('admin.rs.home', compact([
            'doneCount',
            'allCount',
            'periodId',
            'periods',
            'period',
            'status',
            'badge',
            'years',
            'year',
            'data'
        ]));
    }

    public function addAdmin(AddRequest $request, $periodId, $ikId)
    {
        $realization = $request["realization-$ikId"];

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $period = RSPeriod::whereKey($periodId)
            ->where('status', true)->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->firstOrFail();

        $ik = IndikatorKinerja::whereKey($ikId)
            ->where('status', 'aktif')
            ->firstOrFail();

        if ($realization !== null && ($ik->type === 'persen' || $ik->type === 'angka')) {
            if (!is_numeric($realization)) {
                return back()
                    ->withInput()
                    ->withErrors(["realization-$ikId" => 'Realisasi tidak sesuai dengan tipe data']);
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

        $achievement = RSAchievement::whereBelongsTo(auth()->user()->unit)
            ->whereBelongsTo($period, 'period')
            ->whereBelongsTo($ik);

        if ($realization !== null) {
            $achievement = $achievement->firstOrNew();

            if ($ik->type !== 'teks') {
                $realization = (float) $realization;

                if ($realization < 0) {
                    $realization *= -1;
                }

                foreach ([$allAchievement, $periodAchievement] as $key => $instance) {
                    $value = isset($instance->realization) ? (float) $instance->realization : 0;

                    if ($ik->type === 'angka') {
                        if ($value && $achievement->id !== null) {
                            $value -= (float) $achievement->realization;
                        }
                        $value += $realization;
                    } else if ($ik->type === 'persen') {
                        if ($instance->period) {
                            $count = RSAchievement::whereBelongsTo($ik)
                                ->whereBelongsTo($period, 'period')
                                ->whereNull('unit_id')
                                ->count();
                        } else {
                            $count = RSAchievement::whereBelongsTo($ik)
                                ->whereNull('period_id')
                                ->whereNull('unit_id')
                                ->count();
                        }

                        if ($value && $achievement->id !== null) {
                            $value *= $count;
                            $value += $realization - (float) $achievement->realization;
                            $value /= $count;
                        } else if ($value && $achievement->id === null) {
                            $value += $realization;
                            $value /= ($count + 1);
                        } else {
                            $value += $realization;
                        }
                    }
                    if (!ctype_digit("$value")) {
                        $value = number_format($value, 2);
                    }

                    $instance->realization = "$value";
                    $instance->save();
                }
            }

            $achievement->realization = "$realization";

            $achievement->unit()->associate(auth()->user()->unit);
            $achievement->indikatorKinerja()->associate($ik);
            $achievement->period()->associate($period);

            $achievement->save();
        } else {
            $achievement = $achievement->first();

            if ($achievement !== null) {
                foreach ([$allAchievement, $periodAchievement] as $key => $instance) {
                    if ($instance->id !== null && $ik->type !== 'teks') {
                        $value = (float) $instance->realization;

                        if ($ik->type === 'angka') {
                            $value -= (float) $achievement->realization;
                        } else if ($ik->type === 'persen') {
                            if ($instance->period) {
                                $count = RSAchievement::whereBelongsTo($ik)
                                    ->whereBelongsTo($period, 'period')
                                    ->whereNull('unit_id')
                                    ->count();
                            } else {
                                $count = RSAchievement::whereBelongsTo($ik)
                                    ->whereNull('period_id')
                                    ->whereNull('unit_id')
                                    ->count();
                            }

                            $value *= $count;
                            $value -= (float) $achievement->realization;
                            if ($count - 1 > 1) {
                                $value /= $count - 1;
                            }
                        }

                        if (!ctype_digit("$value")) {
                            $value = number_format($value, 2);
                        }

                        $instance->realization = "$value";
                        $instance->save();
                    }
                }

                $achievement->forceDelete();
            }
        }

        return back();
    }
}
