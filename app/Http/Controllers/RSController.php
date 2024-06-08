<?php

namespace App\Http\Controllers;

use App\Http\Requests\RencanaStrategis\AddEvaluationRequest;
use App\Http\Requests\RencanaStrategis\AddTargetRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Requests\RencanaStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use App\Models\RSAchievement;
use Illuminate\Http\Request;
use App\Models\RSEvaluation;
use App\Models\Kegiatan;
use App\Models\RSPeriod;
use App\Models\RSTarget;
use App\Models\RSYear;
use App\Models\Unit;

class RSController extends Controller
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

        $this->checkRoutine($currentYear, $currentPeriod);

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
                                ->withAggregate('evaluation AS evaluation', 'evaluation')
                                ->withAggregate('evaluation AS follow_up', 'follow_up')
                                ->withAggregate('evaluation AS target', 'target')
                                ->withAggregate('evaluation AS done', 'status')
                                ->withCount([
                                    'realization AS count' => function (Builder $query) use ($periodInstance) {
                                        $query->whereNotNull('unit_id');
                                        if ($periodInstance) {
                                            $query->whereBelongsTo($periodInstance, 'period');
                                        } else {
                                            $query->whereNotNull('period_id');
                                        }
                                    }
                                ]);
                        })
                        ->withCount('indikatorKinerja AS rowspan');
                })
                ->orderBy('number')
                ->select(['id', 'number', 'name AS ss'])
                ->withCount([
                    'indikatorKinerja AS rowspan',
                    'indikatorKinerja AS success' => function (Builder $query) {
                        $query->whereHas('evaluation', function (Builder $query) {
                            $query->where('status', true);
                        });
                    },
                    'indikatorKinerja AS failed' => function (Builder $query) {
                        $query->whereDoesntHave('evaluation')
                            ->orWhereHas('evaluation', function (Builder $query) {
                                $query->where('status', false);
                            });
                    },
                ])
                ->get();

            $allCount = $data->sum('rowspan');

            $realizationCount = $data->sum(function (SasaranStrategis $ss) {
                $sum = $ss->kegiatan->sum(function (Kegiatan $k) {
                    $sum = $k->indikatorKinerja->sum('count');
                    return $sum;
                });
                return $sum;
            });

            $unitCount = Unit::where(function (Builder $query) use ($year) {
                $query->whereNotNull('deleted_at')
                    ->whereHas('rencanaStrategis', function (Builder $query) use ($year) {
                        $query->whereHas('period', function (Builder $query) use ($year) {
                            $query->whereHas('year', function (Builder $query) use ($year) {
                                $query->where('year', $year);
                            });
                        });
                    });
            })
                ->orWhereNull('deleted_at')
                ->withTrashed()
                ->count();

            if ($periodInstance === null) {
                $unitCount *= 2;
            }

            $success = $data->sum('success');
            $failed = $data->sum('failed');

            $badge = [
                $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni'),
                $year
            ];

            $data = $data->toArray();

            $periodId = isset($periodInstance) ? $periodInstance->id : null;
        } else {
            $system = false;

            $realizationCount = 0;
            $unitCount = 0;
            $allCount = 0;

            $success = 0;
            $failed = 0;

            $periods = [];

            $period = '';
            $year = '';

            $badge = [];
            $data = [];

            $periodId = null;
        }

        return view('super-admin.achievement.rs.home', compact([
            'realizationCount',
            'unitCount',
            'allCount',
            'periodId',
            'success',
            'periods',
            'period',
            'failed',
            'system',
            'badge',
            'years',
            'year',
            'data',
        ]));
    }

    public function detailView(Request $request, $ikId)
    {
        $status = [
            [
                'text' => 'Tidak tercapai',
                'value' => 0,
            ],
            [
                'text' => 'Tercapai',
                'value' => 1,
            ],
        ];

        if (isset($request->period)) {
            if ($request->period !== '1' && $request->period !== '2' && $request->period !== '3') {
                abort(404);
            }
        }

        $ik = IndikatorKinerja::findOrFail($ikId);

        $k = $ik->kegiatan;
        $ss = $k->sasaranStrategis;

        $yearInstance = $ss->time;

        $periods = array_map(function ($item) {
            $title = 'Januari - Juni';
            if ($item === '2') {
                $title = 'Juli - Desember';
            }
            return [
                'title' => $title,
                'value' => $item
            ];
        }, $yearInstance->periods()
                ->orderBy('period')
                ->pluck('period')
                ->flatten()
                ->unique()
                ->toArray());

        if (count($periods) === 2) {
            $periods[] = [
                'title' => 'Januari - Desember',
                'value' => '3',
            ];
        }

        $year = $yearInstance->year;
        $period = isset($request->period) ? $request->period : end($periods)['value'];

        if ((int) $period > count($periods)) {
            abort(404);
        }

        $periodInstance = $yearInstance->periods()
            ->where('period', $period)
            ->first();

        $data = $ik->realization()
            ->with('unit', function (BelongsTo $query) use ($ik) {
                $query->withTrashed()
                    ->select('id', 'name')
                    ->withAggregate([
                        'rencanaStrategisTarget AS target' => function (Builder $query) use ($ik) {
                            $query->whereBelongsTo($ik);
                        }
                    ], 'target');
            })
            ->where(function (Builder $query) use ($periodInstance) {
                $query->whereNotNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNull('period_id');
                }
            })
            ->select('realization', 'unit_id')
            ->latest()
            ->get()
            ->toArray();

        $realization = $ik->realization()
            ->where(function (Builder $query) use ($periodInstance) {
                $query->whereNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNull('period_id');
                }
            })
            ->first();
        if ($realization) {
            $realization = $realization->realization;
        }

        $evaluation = $ik->evaluation;
        if ($evaluation) {
            $status[$evaluation->status] = [
                ...$status[$evaluation->status],
                'selected' => true,
            ];

            $evaluation = $evaluation->only(['evaluation', 'follow_up', 'status', 'target']);
        }

        $unitCount = Unit::where(function (Builder $query) use ($year, $ik) {
            $query->whereNotNull('deleted_at')
                ->whereHas('rencanaStrategis', function (Builder $query) use ($year, $ik) {
                    $query->whereHas('period', function (Builder $query) use ($year) {
                        $query->whereHas('year', function (Builder $query) use ($year) {
                            $query->where('year', $year);
                        });
                    });
                });
        })
            ->orWhereNull('deleted_at')
            ->withTrashed()
            ->count();

        if ($periodInstance === null) {
            $unitCount *= 2;
        }

        $realizationCount = $ik->realization()
            ->where(function (Builder $query) use ($periodInstance) {
                $query->whereNotNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNotNull('period_id');
                }
            })
            ->count();

        $badge = [
            $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni'),
            $year
        ];

        $ss = $ss->only(['name', 'number']);
        $k = $k->only(['name', 'number']);
        $ik = $ik->only(['id', 'name', 'type', 'number', 'status']);

        return view('super-admin.achievement.rs.detail', compact([
            'realizationCount',
            'realization',
            'evaluation',
            'unitCount',
            'periods',
            'period',
            'status',
            'badge',
            'data',
            'year',
            'ss',
            'ik',
            'k',
        ]));
    }

    public function targetView($year)
    {
        $yearInstance = RSYear::where('year', $year)
            ->firstOrFail();

        $currentYear = Carbon::now()->format('Y');

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('indikatorKinerja', function (Builder $query) {
                $query->where('status', 'aktif')
                    ->whereNot('type', 'teks');
            })
            ->with('kegiatan', function (HasMany $query) {
                $query->whereHas('indikatorKinerja', function (Builder $query) {
                    $query->where('status', 'aktif')
                        ->whereNot('type', 'teks');
                })
                    ->orderBy('number')
                    ->select(['id', 'number', 'name AS k', 'sasaran_strategis_id'])
                    ->with('indikatorKinerja', function (HasMany $query) {
                        $query->where('status', 'aktif')
                            ->whereNot('type', 'teks')
                            ->orderBy('number')
                            ->select(['id', 'type', 'number', 'status', 'name AS ik', 'kegiatan_id'])
                            ->withAggregate('evaluation AS all_target', 'target')
                            ->with('target', function (HasMany $query) {
                                $query->select(['indikator_kinerja_id', 'unit_id', 'target', 'id']);
                            });
                    })
                    ->withCount([
                        'indikatorKinerja AS rowspan' => function (Builder $query) {
                            $query->where('status', 'aktif')
                                ->whereNot('type', 'teks');
                        }
                    ]);
            })
            ->orderBy('number')
            ->select(['id', 'number', 'name AS ss'])
            ->withCount([
                'indikatorKinerja AS rowspan' => function (Builder $query) {
                    $query->where('status', 'aktif')
                        ->whereNot('type', 'teks');
                }
            ])
            ->get()
            ->toArray();

        $units = Unit::where(function (Builder $query) use ($year) {
            $query->whereNotNull('deleted_at')
                ->whereHas('rencanaStrategis', function (Builder $query) use ($year) {
                    $query->whereHas('period', function (Builder $query) use ($year) {
                        $query->whereHas('year', function (Builder $query) use ($year) {
                            $query->where('year', $year);
                        });
                    });
                });
        })
            ->orWhereNull('deleted_at')
            ->select(['short_name', 'name', 'id'])
            ->withTrashed()
            ->latest()
            ->get()
            ->toArray();

        return view('super-admin.achievement.rs.target', compact([
            'units',
            'data',
            'year',
        ]));
    }

    public function periodFirstOrNew($yearId, $value)
    {
        $temp = RSPeriod::firstOrNew([
            'year_id' => $yearId,
            'period' => $value,
        ], [
            'status' => false,
        ]);

        if ($temp->id === null) {
            $temp->save();
        }
    }

    public function checkRoutine($currentYear, $currentPeriod)
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
                        $ik->evaluation()->forceDelete();
                    }
                    $k->indikatorKinerja()->forceDelete();
                }
                $ss->kegiatan()->forceDelete();
            }
            $year->sasaranStrategis()->forceDelete();
            $year->periods()->forceDelete();
            $year->forceDelete();
        }

        $currentPeriod = RSPeriod::where('period', $currentPeriod)
            ->whereHas('year', function (Builder $query) use ($currentYear) {
                $query->where('year', $currentYear);
            })
            ->first();

        if ($currentPeriod) {
            RSPeriod::whereNot('deadline_id', $currentPeriod->id)
                ->update([
                    'deadline_id' => null,
                    'status' => false,
                ]);
        }
    }

    public function statusToggle($periodId)
    {
        $period = RSPeriod::whereKey($periodId)->firstOrFail();

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

            $period->status = true;
            $period->deadline()->associate($currentPeriodInstance);
        }
        $period->save();

        return back();
    }

    public function addEvaluation(AddEvaluationRequest $request, $ikId)
    {
        $ik = IndikatorKinerja::findOrFail($ikId);

        if ($request['period'] === '3') {
            if (!isset($request['target'])) {
                return back()->withErrors(['target' => 'Target wajib diisi']);
            } else if ($ik->type !== 'teks' && !is_numeric($request['target'])) {
                return back()->withErrors(['target' => 'Target harus berupa angka']);
            }

            if ($ik->type === 'teks' && !isset($request['status'])) {
                return back()->withErrors(['status' => 'Status wajib diisi']);
            }
        } else if ($ik->status === 'tidak aktif' && $ik->type !== 'teks' && isset($request['realization'])) {
            if (!is_numeric($request['realization']) && $request['realization'] !== null) {
                return back()->withErrors(['realization' => 'Realisasi harus berupa angka']);
            } else if ((float) $request['realization'] < 0) {
                $request['realization'] *= -1;
                $request['realization'] = "{$request['realization']}";
            }
        }

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $k = $ik->kegiatan;
        $ss = $k->sasaranStrategis;

        $yearInstance = $ss->time;

        $periodInstance = $yearInstance->periods()
            ->where('period', $request['period'])
            ->first();

        if ($ik->type === 'teks' || ($ik->type !== 'teks' && $ik->status === 'tidak aktif' && $periodInstance !== null)) {
            $realization = $ik->realization()
                ->firstOrNew([
                    'period_id' => isset($periodInstance) ? $periodInstance->id : null,
                    'unit_id' => null,
                ]);

            if ($request['realization'] !== null) {
                if ($ik->type !== 'teks') {
                    $temp = $ik->realization()->firstOrNew([
                        'period_id' => null,
                        'unit_id' => null,
                    ]);

                    if ($temp->realization !== null) {
                        $finalRealization = (float) $temp->realization;
                        $currentRealization = $realization->realization !== null ? (float) $realization->realization : 0;

                        if ($ik->type === 'persen') {
                            $count = $ik->realization()
                                ->whereNotNull('period_id')
                                ->whereNull('unit_id')
                                ->count();

                            $finalRealization *= $count;
                            $finalRealization += (float) $request['realization'] - $currentRealization;

                            $count = $count !== 0 ? $count : 1;
                            if ($realization->realization === null) {
                                $count++;
                            }

                            $finalRealization /= $count;
                        } else {
                            $finalRealization += (float) $request['realization'] - $currentRealization;
                        }

                        $temp->realization = "$finalRealization";
                    } else {
                        $temp->realization = $request['realization'];
                    }
                    $temp->save();
                }

                $realization->realization = $request['realization'];
                $realization->save();
            } else if ($realization->id !== null) {
                if ($ik->type !== 'teks') {
                    $temp = $ik->realization()->firstOrNew([
                        'period_id' => null,
                        'unit_id' => null,
                    ]);

                    if ($temp->realization) {
                        $finalRealization = (float) $temp->realization;
                        $currentRealization = $realization->realization !== null ? (float) $realization->realization : 0;

                        if ($ik->type === 'persen') {
                            $count = $ik->realization()
                                ->whereNotNull('period_id')
                                ->whereNull('unit_id')
                                ->count();

                            $finalRealization *= $count;
                        }
                        $finalRealization -= $currentRealization;

                        $temp->realization = "$finalRealization";
                        $temp->save();
                    }
                }

                $realization->forceDelete();
            }
        }

        if ($request['period'] === '3') {
            $evaluation = $ik->evaluation()->firstOrNew();

            $evaluation->evaluation = $request['evaluation'];
            $evaluation->follow_up = $request['follow_up'];
            $evaluation->target = $request['target'];

            if ($ik->type !== 'teks') {
                $realization = $ik->realization()
                    ->whereNull(['period_id', 'unit_id'])
                    ->first();

                if ($realization) {
                    $evaluation->status = (float) $realization->realization >= (float) $request['target'];
                } else {
                    $evaluation->status = false;
                }
            } else {
                $evaluation->status = $request['status'] === '1';
            }

            $evaluation->save();
        }

        return back();
    }

    public function addTarget(AddTargetRequest $request, $ikId, $unitId)
    {
        $ik = IndikatorKinerja::findOrFail($ikId);
        $unit = Unit::withTrashed()->findOrFail($unitId);

        $target = null;
        if (isset($request['target'])) {
            $target = $request['target'][$ikId . '-' . $unitId];
        }

        $targetInstance = RSTarget::firstOrNew([
            'indikator_kinerja_id' => $ikId,
            'unit_id' => $unitId
        ]);

        if ($target === null && $targetInstance->id !== null) {
            $targetInstance->forceDelete();
        } else if ($target !== null) {
            $targetInstance->target = $target;
            $targetInstance->save();
        }

        $allTarget = RSTarget::whereBelongsTo($ik)
            ->get();

        $sumAllTarget = $allTarget->sum('target');
        $countAllTarget = $allTarget->count();

        $evaluation = RSEvaluation::firstOrNew([
            'indikator_kinerja_id' => $ikId
        ]);

        if ($ik->type === 'persen') {
            $sumAllTarget = $countAllTarget !== 0 ? $sumAllTarget / $countAllTarget : 0;
        }

        $realization = RSAchievement::whereBelongsTo($ik)
            ->whereNull(['period_id', 'unit_id'])
            ->first();

        $evaluation->target = $sumAllTarget;

        $evaluation->status = false;
        if ($realization !== null) {
            if ((float) $evaluation->realization >= $sumAllTarget) {
                $evaluation->status = true;
            }
        }

        $evaluation->save();

        return back();
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
                ->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance) {
                    $query->where('status', 'aktif');
                    if ($statusIndex === 1) {
                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    } else if ($statusIndex === 2) {
                        $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    }
                })
                ->with('kegiatan', function (HasMany $query) use ($statusIndex, $periodInstance) {
                    $query->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance) {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        }
                    })
                        ->orderBy('number')
                        ->select(['id', 'number', 'name AS k', 'sasaran_strategis_id'])
                        ->with('indikatorKinerja', function (HasMany $query) use ($statusIndex, $periodInstance) {
                            if ($statusIndex === 1) {
                                $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            } else if ($statusIndex === 2) {
                                $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
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
                            'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance) {
                                $query->where('status', 'aktif');
                                if ($statusIndex === 1) {
                                    $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                        $query->whereBelongsTo(auth()->user()->unit)
                                            ->whereBelongsTo($periodInstance, 'period');
                                    });
                                } else if ($statusIndex === 2) {
                                    $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                        $query->whereBelongsTo(auth()->user()->unit)
                                            ->whereBelongsTo($periodInstance, 'period');
                                    });
                                }
                            }
                        ]);
                })
                ->orderBy('number')
                ->select(['id', 'number', 'name AS ss'])
                ->withCount([
                    'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance) {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
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
                    'indikatorKinerja AS done' => function (Builder $query) use ($periodInstance) {
                        $query->where('status', 'aktif')
                            ->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
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
            ->whereHas('kegiatan', function (Builder $query) use ($period) {
                $query->whereHas('sasaranStrategis', function (Builder $query) use ($period) {
                    $query->whereBelongsTo($period->year, 'time');
                });
            })
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

        $unitAchievement = RSAchievement::firstOrNew([
            'unit_id' => auth()->user()->unit->id,
            'indikator_kinerja_id' => $ik->id,
            'period_id' => null,
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

                foreach ([$allAchievement, $periodAchievement, $unitAchievement] as $key => $instance) {
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
                                ->whereNotNull('unit_id')
                                ->count();
                        } else if ($instance->unit) {
                            $count = RSAchievement::whereBelongsTo($ik)
                                ->whereBelongsTo(auth()->user()->unit)
                                ->whereNotNull('period_id')
                                ->count();
                        } else {
                            $count = RSAchievement::whereBelongsTo($ik)
                                ->whereNotNull('period_id')
                                ->whereNotNull('unit_id')
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
                foreach ([$allAchievement, $periodAchievement, $unitAchievement] as $key => $instance) {
                    if ($instance->id !== null && $ik->type !== 'teks') {
                        $value = (float) $instance->realization;

                        if ($ik->type === 'angka') {
                            $value -= (float) $achievement->realization;
                        } else if ($ik->type === 'persen') {
                            if ($instance->period) {
                                $count = RSAchievement::whereBelongsTo($ik)
                                    ->whereBelongsTo($period, 'period')
                                    ->whereNotNull('unit_id')
                                    ->count();
                            } else if ($instance->unit) {
                                $count = RSAchievement::whereBelongsTo($ik)
                                    ->whereBelongsTo(auth()->user()->unit)
                                    ->whereNotNull('period_id')
                                    ->count();
                            } else {
                                $count = RSAchievement::whereBelongsTo($ik)
                                    ->whereNotNull('period_id')
                                    ->whereNotNull('unit_id')
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

        if ($ik->type !== 'teks') {
            $evaluation = RSEvaluation::firstOrNew([
                'indikator_kinerja_id' => $ik->id,
            ], [
                'target' => '0',
            ]);

            $evaluation->status = (float) $allAchievement->realization >= (float) $evaluation->target;
            $evaluation->save();
        }

        return back();
    }

    public function historyAdmin(Request $request)
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

        $years = RSPeriod::where('status', false)
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
                ->where('status', false)
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
            $periodInstance = RSPeriod::where('status', false)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->firstOrFail();

            $data = $yearInstance->sasaranStrategis()
                ->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance) {
                    $query->where('status', 'aktif');
                    if ($statusIndex === 1) {
                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    } else if ($statusIndex === 2) {
                        $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    }
                })
                ->with('kegiatan', function (HasMany $query) use ($statusIndex, $periodInstance) {
                    $query->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance) {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        }
                    })
                        ->orderBy('number')
                        ->select(['id', 'number', 'name AS k', 'sasaran_strategis_id'])
                        ->with('indikatorKinerja', function (HasMany $query) use ($statusIndex, $periodInstance) {
                            if ($statusIndex === 1) {
                                $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            } else if ($statusIndex === 2) {
                                $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
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
                            'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance) {
                                $query->where('status', 'aktif');
                                if ($statusIndex === 1) {
                                    $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                        $query->whereBelongsTo(auth()->user()->unit)
                                            ->whereBelongsTo($periodInstance, 'period');
                                    });
                                } else if ($statusIndex === 2) {
                                    $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                        $query->whereBelongsTo(auth()->user()->unit)
                                            ->whereBelongsTo($periodInstance, 'period');
                                    });
                                }
                            }
                        ]);
                })
                ->orderBy('number')
                ->select(['id', 'number', 'name AS ss'])
                ->withCount([
                    'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance) {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance) {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        }
                    }
                ])
                ->get()
                ->toArray();

            $badge = [
                $period === '1' ? 'Januari - Juni' : 'Juli - Desember',
                $year
            ];
        } else {
            $periods = [];

            $period = '';
            $year = '';

            $badge = [];
            $data = [];
        }

        return view('admin.history.rs.home', compact([
            'periods',
            'period',
            'status',
            'badge',
            'years',
            'year',
            'data',
        ]));
    }
}