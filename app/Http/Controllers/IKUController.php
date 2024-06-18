<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaUtama\AddEvaluationRequest;
use App\Http\Requests\IndikatorKinerjaUtama\AddTargetRequest;
use App\Http\Requests\IndikatorKinerjaUtama\AddRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Models\IndikatorKinerjaProgram;
use App\Models\IKUAchievementData;
use Illuminate\Support\Carbon;
use App\Models\IKUAchievement;
use App\Models\IKUEvaluation;
use Illuminate\Http\Request;
use App\Exports\IKUExport;
use App\Models\IKUPeriod;
use App\Models\IKUTarget;
use App\Models\IKUYear;
use App\Models\Unit;
use Maatwebsite\Excel\Facades\Excel;

class IKUController extends Controller
{
    /*
    | -----------------------------------------------------------------
    | SUPER ADMIN
    | -----------------------------------------------------------------
    */

    public function homeView(Request $request)
    {
        if (isset($request->year) && !is_numeric($request->year)) {
            abort(404);
        }

        $currentYearInstance = IKUYear::currentTime();

        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $temp = $key + 1;
                $currentPeriod = "$temp";

                break;
            }
        }

        $this->checkRoutine($currentYear, $currentPeriod);

        $years = IKUYear::orderBy('year')
            ->pluck('year')
            ->toArray();

        $year = isset($request->year) ? $request->year : end($years);
        $yearInstance = IKUYear::where('year', $year)->firstOrFail();

        if (
            ($year !== $currentYear && $yearInstance->periods->count() !== 4)
            ||
            ($year === $currentYear && $yearInstance->periods->count() < (int) $currentPeriod)
        ) {
            foreach (['1', '2', '3', '4'] as $key => $value) {
                if ($year !== $currentYear || (int) $value <= (int) $currentPeriod) {
                    $this->periodFirstOrNew($yearInstance->id, $value);
                }
            }
        }

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->select([
                'period',
                'status',
                'id',
            ])
            ->get()
            ->map(function ($item) {
                $title = 'TW 1 | Jan - Mar';
                if ($item['period'] === '2') {
                    $title = 'TW 2 | Apr - Jun';
                } else if ($item['period'] === '3') {
                    $title = 'TW 3 | Jul - Sep';
                } else if ($item['period'] === '4') {
                    $title = 'TW 4 | Okt - Des';
                }

                return [
                    ...$item->toArray(),
                    'title' => $title,
                ];
            });

        $data = $yearInstance->sasaranKegiatan()
            ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram')
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query) {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram')
                        ->select([
                            'name AS ikk',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerjaProgram')
                        ->select([
                            'name AS ps',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number')
                        ->withCount('indikatorKinerjaProgram AS rowspan');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->select([
                        'name AS ikp',
                        'definition',
                        'status',
                        'type',
                        'id',

                        'program_strategis_id',
                    ])
                        ->orderBy('number')
                        ->withCount([
                            'achievements AS tw1' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '1');
                                });
                            },
                            'achievements AS tw2' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '2');
                                });
                            },
                            'achievements AS tw3' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '3');
                                });
                            },
                            'achievements AS tw4' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '4');
                                });
                            },
                            'achievements AS all',
                        ])
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS done', 'status');
                },
            ])
            ->select([
                'name AS sk',
                'number',
                'id',
            ])
            ->orderBy('number')
            ->get()
            ->map(function ($item) {
                $temp = $item->indikatorKinerjaKegiatan->map(function ($item) {
                    return [
                        ...$item->toArray(),

                        'rowspan' => $item->programStrategis->sum('rowspan')
                    ];
                });

                return [
                    ...$item->only(['number', 'sk', 'id']),

                    'indikator_kinerja_kegiatan' => $temp->toArray(),
                    'rowspan' => $temp->sum('rowspan'),
                ];
            })
            ->toArray();

        $badge = [$year];

        return view('super-admin.achievement.iku.home', compact([
            'periods',
            'badge',
            'years',
            'year',
            'data',
        ]));
    }

    public function detailView(Request $request, IndikatorKinerjaProgram $ikp)
    {
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3', '4', '5'])) {
            abort(404);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $yearInstance = $sk->time;
        $year = $yearInstance->year;

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item) {
                $title = 'TW 1 | Jan - Mar';
                if ($item === '2') {
                    $title = 'TW 2 | Apr - Jun';
                } else if ($item === '3') {
                    $title = 'TW 3 | Jul - Sep';
                } else if ($item === '4') {
                    $title = 'TW 4 | Okt - Des';
                }

                return [
                    'title' => $title,
                    'value' => $item
                ];
            });

        if ($periods->count() === 4) {
            $periods->push([
                'title' => 'Januari - Desember',
                'value' => '5'
            ]);
        }

        $period = isset($request->period) ? $request->period : $periods->last()['value'];

        if ((int) $period > $periods->count()) {
            abort(404);
        }

        $periodInstance = $yearInstance->periods()
            ->where('period', $period)
            ->first();

        $columns = $ikp->columns()
            ->select([
                'file',
                'name',
                'id',
            ])
            ->orderBy('number')
            ->get()
            ->toArray();

        $data = IKUAchievement::withTrashed()
            ->with([
                'data' => function (HasMany $query) {
                    $query->select([
                        'achievement_id',
                        'column_id',
                        'data',
                    ])
                        ->withAggregate('column AS file', 'file');
                }
            ])
            ->where(function (Builder $query) use ($periodInstance) {
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                }
            })
            ->whereBelongsTo($ikp)
            ->select('id')
            ->withAggregate('unit AS unit', 'name')
            ->latest()
            ->get();

        $achievementCount = $data->count();
        $data = $data->groupBy('unit')->toArray();

        $evaluation = $ikp->evaluation;

        $sk = $sk->only([
            'number',
            'name',
        ]);

        $ikk = $ikk->only([
            'number',
            'name',
        ]);

        $ps = $ps->only([
            'number',
            'name',
        ]);

        $ikp = $ikp->only([
            'definition',
            'number',
            'status',
            'name',
            'type',
            'id',
        ]);

        $badge = [
            $periods->firstWhere('value', $period)['title'],
            $year
        ];

        $periods = $periods->toArray();

        return view('super-admin.achievement.iku.detail', compact([
            'achievementCount',
            'evaluation',
            'columns',
            'periods',
            'period',
            'badge',
            'data',
            'year',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }

    public function targetView($year)
    {
        $yearInstance = IKUYear::where('year', $year)
            ->firstOrFail();

        $data = $yearInstance->sasaranKegiatan()
            ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram', function (Builder $query) {
                $query->where('status', 'aktif');
            })
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query) {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram', function (Builder $query) {
                        $query->where('status', 'aktif');
                    })
                        ->select([
                            'name AS ikk',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerjaProgram', function (Builder $query) {
                        $query->where('status', 'aktif');
                    })
                        ->select([
                            'name AS ps',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number')
                        ->withCount([
                            'indikatorKinerjaProgram AS rowspan' => function (Builder $query) {
                                $query->where('status', 'aktif');
                            }
                        ]);
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->where('status', 'aktif')
                        ->select([
                            'name AS ikp',
                            'definition',
                            'type',
                            'id',

                            'program_strategis_id',
                        ])
                        ->orderBy('number')
                        ->with('target', function (HasMany $query) {
                            $query->select([
                                'target',
                                'id',

                                'indikator_kinerja_program_id',
                                'unit_id',
                            ]);
                        })
                        ->withAggregate('evaluation AS allTarget', 'target');
                },
            ])
            ->orderBy('number')
            ->select([
                'name AS sk',
                'number',
                'id',
            ])
            ->get()
            ->map(function ($item) {
                $temp = $item->indikatorKinerjaKegiatan->map(function ($item) {
                    return [
                        ...$item->toArray(),

                        'rowspan' => $item->programStrategis->sum('rowspan')
                    ];
                });

                return [
                    ...$item->only([
                        'number',
                        'sk',
                        'id',
                    ]),

                    'indikator_kinerja_kegiatan' => $temp->toArray(),
                    'rowspan' => $temp->sum('rowspan'),
                ];
            })
            ->toArray();

        $units = Unit::where(function (Builder $query) use ($year) {
            $query->whereNotNull('deleted_at')
                ->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year) {
                    $query->whereHas('period', function (Builder $query) use ($year) {
                        $query->whereHas('year', function (Builder $query) use ($year) {
                            $query->where('year', $year);
                        });
                    });
                });
        })
            ->orWhereNull('deleted_at')
            ->select([
                'short_name',
                'name',
                'id',
            ])
            ->withTrashed()
            ->latest()
            ->get()
            ->toArray();

        return view('super-admin.achievement.iku.target', compact([
            'units',
            'data',
            'year',
        ]));
    }

    public function periodFirstOrNew($yearId, $value)
    {
        $temp = IKUPeriod::firstOrNew([
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
        $currentPeriod = IKUPeriod::where('period', $currentPeriod)
            ->whereHas('year', function (Builder $query) use ($currentYear) {
                $query->where('year', $currentYear);
            })
            ->first();

        if ($currentPeriod) {
            IKUPeriod::whereNot('deadline_id', $currentPeriod->id)
                ->update([
                    'deadline_id' => null,
                    'status' => false,
                ]);
        }
    }

    public function statusToggle(IKUPeriod $period)
    {
        if ($period->status) {
            $period->status = false;
            $period->deadline()->dissociate();
        } else {
            $currentMonth = (int) Carbon::now()->format('m');
            $currentPeriod = '1';

            foreach ([3, 6, 9, 12] as $key => $value) {
                if ($currentMonth <= $value) {
                    $temp = $key + 1;
                    $currentPeriod = "$temp";

                    break;
                }
            }

            $currentYearInstance = IKUYear::currentTime();
            $currentPeriodInstance = $currentYearInstance->periods()
                ->where('period', $currentPeriod)
                ->firstOrFail();

            $period->deadline()->associate($currentPeriodInstance);
            $period->status = true;
        }
        $period->save();

        return back();
    }

    public function addEvaluation(AddEvaluationRequest $request, IndikatorKinerjaProgram $ikp)
    {
        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $yearInstance = $sk->time;

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period');

        if ($periods->count() === 4) {
            $evaluation = $ikp->evaluation()
                ->firstOrNew([], [
                    'status' => false,
                    'target' => 0,
                ]);

            $evaluation->evaluation = $request['evaluation'];
            $evaluation->follow_up = $request['follow_up'];

            $achievementCount = $ikp->achievements->count();
            if ($achievementCount) {
                $evaluation->status = $achievementCount >= $evaluation->target;
            }

            $evaluation->save();

            return back();
        }

        abort(404);
    }

    public function addTarget(AddTargetRequest $request, $ikpId, $unitId)
    {
        $ikp = IndikatorKinerjaProgram::findOrFail($ikpId);

        if ($ikp->status === 'aktif') {
            Unit::withTrashed()->findOrFail($unitId);

            $target = null;
            if (isset($request['target'])) {
                $target = $request['target'][$ikpId . '-' . $unitId];
            }

            $targetInstance = IKUTarget::firstOrNew([
                'indikator_kinerja_program_id' => $ikpId,
                'unit_id' => $unitId
            ]);

            if ($target === null && $targetInstance->id !== null) {
                $targetInstance->forceDelete();
            } else if ($target !== null) {
                $targetInstance->target = (int) $target;
                $targetInstance->save();
            }

            $sumAllTarget = $ikp->target()
                ->sum('target');

            $realization = $ikp->achievements()
                ->count();

            $evaluation = IKUEvaluation::firstOrNew([
                'indikator_kinerja_program_id' => $ikpId
            ]);

            $evaluation->status = $realization >= $sumAllTarget;
            $evaluation->target = $sumAllTarget;

            $evaluation->save();

            return back();
        }

        abort(404);
    }

    public function exportIKU(Request $request)
    {
        if (isset($request->year) && !is_numeric($request->year)) {
            abort(404);
        }

        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $temp = $key + 1;
                $currentPeriod = "$temp";

                break;
            }
        }

        $years = IKUYear::orderBy('year')
            ->pluck('year')
            ->toArray();

        $year = isset($request->year) ? $request->year : end($years);
        $yearInstance = IKUYear::where('year', $year)->firstOrFail();

        if (
            ($year !== $currentYear && $yearInstance->periods->count() !== 4)
            ||
            ($year === $currentYear && $yearInstance->periods->count() < (int) $currentPeriod)
        ) {
            foreach (['1', '2', '3', '4'] as $key => $value) {
                if ($year !== $currentYear || (int) $value <= (int) $currentPeriod) {
                    $this->periodFirstOrNew($yearInstance->id, $value);
                }
            }
        }

        $data = $yearInstance->sasaranKegiatan()
            ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram')
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query) {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram')
                        ->select([
                            'name',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerjaProgram')
                        ->select([
                            'name',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->select([
                        'definition',
                        'name',
                        'type',
                        'id',

                        'program_strategis_id',
                    ])
                        ->orderBy('number')
                        ->withCount([
                            'achievements AS tw1' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '1');
                                });
                            },
                            'achievements AS tw2' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '2');
                                });
                            },
                            'achievements AS tw3' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '3');
                                });
                            },
                            'achievements AS tw4' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '4');
                                });
                            },
                            'achievements AS all',
                        ])
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS done', 'status');
                },
            ])
            ->select([
                'number',
                'name',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $collection = collect([
            ['tahun', $year],
            [
                'no',
                'sasaran kegiatan',
                'indikator kinerja kegiatan',
                'program strategis',
                'indikator kinerja program',
                'tipe',
                'definisi operasional',
                "target $year",
                "realisasi $year",
                'tw1',
                'tw2',
                'tw3',
                'tw4',
                'kendala',
                'tindak lanjut',
                'status',
            ]
        ]);

        $data->each(function ($sk) use ($collection) {
            $sk->indikatorKinerjaKegiatan->each(function ($ikk, $ikkIndex) use ($collection, $sk) {
                $ikk->programStrategis->each(function ($ps, $psIndex) use ($collection, $ikkIndex, $ikk, $sk) {
                    $ps->indikatorKinerjaProgram->each(function ($ikp, $ikpIndex) use ($collection, $ikkIndex, $psIndex, $ikk, $ps, $sk) {
                        $temp = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];

                        if (!$ikpIndex) {
                            if (!$psIndex) {
                                if (!$ikkIndex) {
                                    $temp[0] = $sk->number;
                                    $temp[1] = $sk->name;
                                }
                                $temp[2] = $ikk->name;
                            }
                            $temp[3] = $ps->name;
                        }

                        $temp[4] = $ikp->name;
                        $temp[5] = $ikp->type;
                        $temp[6] = $ikp->definition;
                        $temp[7] = $ikp->target;
                        $temp[8] = $ikp->all;

                        $temp[9] = $ikp->tw1;
                        $temp[10] = $ikp->tw2;
                        $temp[11] = $ikp->tw3;
                        $temp[12] = $ikp->tw4;

                        $temp[13] = $ikp->evaluation;
                        $temp[14] = $ikp->follow_up;
                        $temp[15] = $ikp->done ? 'Tercapai' : 'Tidak tercapai';

                        $collection->add($temp);
                    });
                });
            });
        });

        return Excel::download(new IKUExport($collection->toArray()), 'indikator-kinerja-utama.xlsx');
    }

    public function detailExportIKU(Request $request, IndikatorKinerjaProgram $ikp)
    {
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3', '4', '5'])) {
            abort(404);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $yearInstance = $sk->time;
        $year = $yearInstance->year;

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item) {
                $title = 'TW 1 | Jan - Mar';
                if ($item === '2') {
                    $title = 'TW 2 | Apr - Jun';
                } else if ($item === '3') {
                    $title = 'TW 3 | Jul - Sep';
                } else if ($item === '4') {
                    $title = 'TW 4 | Okt - Des';
                }

                return [
                    'title' => $title,
                    'value' => $item
                ];
            });

        if ($periods->count() === 4) {
            $periods->push([
                'title' => 'Januari - Desember',
                'value' => '5'
            ]);
        }

        $period = isset($request->period) ? $request->period : $periods->last()['value'];

        if ((int) $period > $periods->count()) {
            abort(404);
        }

        $periodInstance = $yearInstance->periods()
            ->where('period', $period)
            ->first();

        $columns = $ikp->columns()
            ->select([
                'file',
                'name',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $data = IKUAchievement::withTrashed()
            ->with([
                'data' => function (HasMany $query) {
                    $query->select([
                        'achievement_id',
                        'column_id',
                        'data',
                    ])
                        ->withAggregate('column AS file', 'file');
                }
            ])
            ->where(function (Builder $query) use ($periodInstance) {
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                }
            })
            ->whereBelongsTo($ikp)
            ->select('id')
            ->withAggregate('unit AS unit', 'name')
            ->latest()
            ->get();

        $achievementCount = $data->count();
        $data = $data->groupBy('unit');

        $evaluation = $ikp->evaluation;

        $collection = collect([
            ['tahun', $year],
            ['periode', $periods->firstWhere('value', $period)['title']],
            ['no', $sk->number, 'sasaran kegiatan', $sk->name],
            ['no', $ikk->number, 'indikator kinerja kegiatan', $ikk->name],
            ['no', $ps->number, 'program strategis', $ps->name],
            ['no', $ikp->number, 'indikator kinerja program', $ikp->name],
            ['definisi operasional', $ikp->definition, 'tipe', $ikp->type],
            ['realisasi', $achievementCount],
            $evaluation && $period === '5' ? ['target', $evaluation->target, 'kendala', $evaluation->evaluation, 'tindak lanjut', $evaluation->follow_up] : [],
            ['data'],
            [
                'no',
                ...$columns->map(function ($column) {
                    return $column->name;
                })
            ],
        ]);

        $data->each(function ($item, $key) use ($collection, $columns) {
            $collection->add([$key]);
            $item->each(function ($col, $index) use ($collection, $columns) {
                $temp = collect([$index + 1]);

                $columns->each(function ($column) use ($collection, $temp, $col) {
                    $find = $col['data']->firstWhere('column_id', $column->id);

                    if ($find) {
                        if ($find->file) {
                            $temp->add(url(asset('storage/' . $find->data)));
                        } else {
                            $temp->add($find->data);
                        }
                    } else {
                        $temp->add('');
                    }
                });

                $collection->add($temp->toArray());
            });
        });

        return Excel::download(new IKUExport($collection->toArray()), $ikp->name . '.xlsx');
    }


    /*
    | -----------------------------------------------------------------
    | ADMIN
    | -----------------------------------------------------------------
    */

    public function homeViewAdmin(Request $request)
    {
        if (!is_numeric($request->year) && isset($request->year)) {
            abort(404);
        }
        if (!in_array($request->period, ['1', '2', '3', '4']) && isset($request->period)) {
            abort(404);
        }

        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $temp = $key + 1;
                $currentPeriod = "$temp";

                break;
            }
        }

        $years = IKUPeriod::where('status', true)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->withAggregate('year AS year', 'year')
            ->orderBy('year')
            ->pluck('year')
            ->flatten()
            ->unique()
            ->toArray();

        $periods = [];
        $badge = [];
        $data = [];

        $period = '';
        $year = '';

        if (count($years)) {
            $year = isset($request->year) ? $request->year : end($years);
            $yearInstance = IKUYear::where('year', $year)->firstOrFail();

            $periods = $yearInstance->periods()
                ->where('status', true)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->orderBy('period')
                ->pluck('period')
                ->map(function ($item) {
                    $title = 'TW 1 | Jan - Mar';
                    if ($item === '2') {
                        $title = 'TW 2 | Apr - Jun';
                    } else if ($item === '3') {
                        $title = 'TW 3 | Jul - Sep';
                    } else if ($item === '4') {
                        $title = 'TW 4 | Okt - Des';
                    }

                    return [
                        'title' => $title,
                        'value' => $item,
                    ];
                });

            $period = isset($request->period) ? $request->period : $periods->last()['value'];
            $periodInstance = $yearInstance->periods()
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->where('period', $period)
                ->where('status', true)
                ->firstOrFail();

            $data = $yearInstance->sasaranKegiatan()
                ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram', function (Builder $query) {
                    $query->where('status', 'aktif');
                })
                ->with([
                    'indikatorKinerjaKegiatan' => function (HasMany $query) {
                        $query->whereHas('programStrategis.indikatorKinerjaProgram', function (Builder $query) {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS ikk',
                                'id',

                                'sasaran_kegiatan_id',
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                        $query->whereHas('indikatorKinerjaProgram', function (Builder $query) {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS ps',
                                'id',

                                'indikator_kinerja_kegiatan_id',
                            ])
                            ->withCount([
                                'indikatorKinerjaProgram AS rowspan' => function (Builder $query) {
                                    $query->where('status', 'aktif');
                                }
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) use ($periodInstance) {
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select([
                                'name AS ikp',
                                'definition',
                                'type',
                                'id',

                                'program_strategis_id',
                            ])
                            ->withAggregate([
                                'target AS target' => function (Builder $query) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                }
                            ], 'target')
                            ->withCount([
                                'achievements AS all' => function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                },
                                'achievements AS achievements' => function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ]);
                    },
                ])
                ->orderBy('number')
                ->select([
                    'name AS sk',
                    'number',
                    'id',
                ])
                ->get()
                ->map(function ($item) {
                    $temp = $item->indikatorKinerjaKegiatan->map(function ($item) {
                        return [
                            ...$item->toArray(),

                            'rowspan' => $item->programStrategis->sum('rowspan')
                        ];
                    });

                    return [
                        ...$item->only(['number', 'sk', 'id']),

                        'indikator_kinerja_kegiatan' => $temp->toArray(),
                        'rowspan' => $temp->sum('rowspan'),
                    ];
                })
                ->toArray();

            $periodReq = $periods->firstWhere('value', $period);
            $badge = [$periodReq['title'], $year];

            $periods = $periods->toArray();
        }

        return view('admin.iku.home', compact([
            'periods',
            'period',
            'badge',
            'years',
            'year',
            'data',
        ]));
    }

    public function detailViewAdmin(Request $request, IndikatorKinerjaProgram $ikp)
    {
        if ($ikp->status !== 'aktif') {
            abort(404);
        }
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3', '4'])) {
            abort(404);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $temp = $key + 1;
                $currentPeriod = "$temp";

                break;
            }
        }

        $periods = $year->periods()
            ->where('status', true)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item) {
                $title = 'TW 1 | Jan - Mar';
                if ($item === '2') {
                    $title = 'TW 2 | Apr - Jun';
                } else if ($item === '3') {
                    $title = 'TW 3 | Jul - Sep';
                } else if ($item === '4') {
                    $title = 'TW 4 | Okt - Des';
                }

                return [
                    'title' => $title,
                    'value' => $item,
                ];
            });

        if (!$periods->count()) {
            abort(404);
        }

        $period = isset($request->period) ? $request->period : $periods->last()['value'];
        $periodInstance = $year->periods()
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->where('period', $period)
            ->where('status', true)
            ->firstOrFail();

        $columns = $ikp->columns()
            ->select([
                'file',
                'name',
                'id'
            ])
            ->orderBy('number')
            ->get()
            ->toArray();

        $data = $periodInstance->achievements()
            ->with('data', function (HasMany $query) {
                $query->select([
                    'data',

                    'achievement_id',
                    'column_id',
                ])
                    ->withAggregate('column AS file', 'file');
            })
            ->whereBelongsTo(auth()->user()->unit)
            ->whereBelongsTo($ikp)
            ->select('id')
            ->latest()
            ->get()
            ->toArray();

        $target = $ikp->target()
            ->whereBelongsTo(auth()->user()->unit)
            ->first();
        if ($target) {
            $target = $target->target;
        }

        $all = $ikp->achievements()
            ->whereBelongsTo(auth()->user()->unit)
            ->count();

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);
        $ikk = $ikk->only([
            'number',
            'name',
            'id',
        ]);
        $ps = $ps->only([
            'number',
            'name',
            'id',
        ]);
        $ikp = $ikp->only([
            'definition',
            'number',
            'name',
            'type',
            'id',
        ]);

        $badge = [$periods->firstWhere('value', $period)['title'], $year->year];
        $periods = $periods->toArray();

        return view('admin.iku.detail', compact([
            'columns',
            'periods',
            'period',
            'target',
            'badge',
            'data',
            'all',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }

    public function historyAdmin(Request $request)
    {
        if (!is_numeric($request->year) && isset($request->year)) {
            abort(404);
        }
        if (!in_array($request->period, ['1', '2', '3', '4']) && isset($request->period)) {
            abort(404);
        }

        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $temp = $key + 1;
                $currentPeriod = "$temp";

                break;
            }
        }

        $years = IKUPeriod::where('status', false)
            ->withAggregate('year AS year', 'year')
            ->orderBy('year')
            ->pluck('year')
            ->flatten()
            ->unique()
            ->toArray();

        $periods = [];
        $badge = [];
        $data = [];

        $period = '';
        $year = '';

        if (count($years)) {
            $year = isset($request->year) ? $request->year : end($years);
            $yearInstance = IKUYear::where('year', $year)->firstOrFail();

            $periods = $yearInstance->periods()
                ->where('status', false)
                ->orderBy('period')
                ->pluck('period')
                ->map(function ($item) {
                    $title = 'TW 1 | Jan - Mar';
                    if ($item === '2') {
                        $title = 'TW 2 | Apr - Jun';
                    } else if ($item === '3') {
                        $title = 'TW 3 | Jul - Sep';
                    } else if ($item === '4') {
                        $title = 'TW 4 | Okt - Des';
                    }

                    return [
                        'title' => $title,
                        'value' => $item,
                    ];
                });

            $period = isset($request->period) ? $request->period : $periods->last()['value'];
            $periodInstance = $yearInstance->periods()
                ->where('period', $period)
                ->where('status', false)
                ->firstOrFail();

            $data = $yearInstance->sasaranKegiatan()
                ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram', function (Builder $query) {
                    $query->where('status', 'aktif');
                })
                ->with([
                    'indikatorKinerjaKegiatan' => function (HasMany $query) {
                        $query->whereHas('programStrategis.indikatorKinerjaProgram', function (Builder $query) {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS ikk',
                                'id',

                                'sasaran_kegiatan_id',
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                        $query->whereHas('indikatorKinerjaProgram', function (Builder $query) {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS ps',
                                'id',

                                'indikator_kinerja_kegiatan_id',
                            ])
                            ->withCount([
                                'indikatorKinerjaProgram AS rowspan' => function (Builder $query) {
                                    $query->where('status', 'aktif');
                                }
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) use ($periodInstance) {
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select([
                                'name AS ikp',
                                'definition',
                                'type',
                                'id',

                                'program_strategis_id',
                            ])
                            ->withAggregate([
                                'target AS target' => function (Builder $query) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                }
                            ], 'target')
                            ->withCount([
                                'achievements AS all' => function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                },
                                'achievements AS achievements' => function (Builder $query) use ($periodInstance) {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ]);
                    },
                ])
                ->orderBy('number')
                ->select([
                    'name AS sk',
                    'number',
                    'id',
                ])
                ->get()
                ->map(function ($item) {
                    $temp = $item->indikatorKinerjaKegiatan->map(function ($item) {
                        return [
                            ...$item->toArray(),

                            'rowspan' => $item->programStrategis->sum('rowspan')
                        ];
                    });

                    return [
                        ...$item->only(['number', 'sk', 'id']),

                        'indikator_kinerja_kegiatan' => $temp->toArray(),
                        'rowspan' => $temp->sum('rowspan'),
                    ];
                })
                ->toArray();

            $periodReq = $periods->firstWhere('value', $period);
            $badge = [$periodReq['title'], $year];

            $periods = $periods->toArray();
        }

        return view('admin.history.iku.home', compact([
            'periods',
            'period',
            'badge',
            'years',
            'year',
            'data',
        ]));
    }

    public function historyDetailAdmin(Request $request, IndikatorKinerjaProgram $ikp)
    {
        if ($ikp->status !== 'aktif') {
            abort(404);
        }
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3', '4'])) {
            abort(404);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $temp = $key + 1;
                $currentPeriod = "$temp";

                break;
            }
        }

        $periods = $year->periods()
            ->where('status', false)
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item) {
                $title = 'TW 1 | Jan - Mar';
                if ($item === '2') {
                    $title = 'TW 2 | Apr - Jun';
                } else if ($item === '3') {
                    $title = 'TW 3 | Jul - Sep';
                } else if ($item === '4') {
                    $title = 'TW 4 | Okt - Des';
                }

                return [
                    'title' => $title,
                    'value' => $item,
                ];
            });

        if (!$periods->count()) {
            abort(404);
        }

        $period = isset($request->period) ? $request->period : $periods->last()['value'];
        $periodInstance = $year->periods()
            ->where('period', $period)
            ->where('status', false)
            ->firstOrFail();

        $columns = $ikp->columns()
            ->select([
                'file',
                'name',
                'id'
            ])
            ->orderBy('number')
            ->get()
            ->toArray();

        $data = $periodInstance->achievements()
            ->with('data', function (HasMany $query) {
                $query->select([
                    'data',

                    'achievement_id',
                    'column_id',
                ])
                    ->withAggregate('column AS file', 'file');
            })
            ->whereBelongsTo(auth()->user()->unit)
            ->whereBelongsTo($ikp)
            ->select('id')
            ->latest()
            ->get()
            ->toArray();

        $target = $ikp->target()
            ->whereBelongsTo(auth()->user()->unit)
            ->first();
        if ($target) {
            $target = $target->target;
        }

        $all = $ikp->achievements()
            ->whereBelongsTo(auth()->user()->unit)
            ->count();

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);
        $ikk = $ikk->only([
            'number',
            'name',
            'id',
        ]);
        $ps = $ps->only([
            'number',
            'name',
            'id',
        ]);
        $ikp = $ikp->only([
            'definition',
            'number',
            'name',
            'type',
            'id',
        ]);

        $badge = [$periods->firstWhere('value', $period)['title'], $year->year];
        $periods = $periods->toArray();

        return view('admin.history.iku.detail', compact([
            'columns',
            'periods',
            'period',
            'target',
            'badge',
            'data',
            'all',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }

    public function addData(AddRequest $request, $period, IndikatorKinerjaProgram $ikp)
    {
        if ($ikp->status === 'aktif') {
            $columns = $ikp->columns()
                ->orderBy('number')
                ->get();

            $inputExists = false;
            foreach ($columns->where('file', false) as $key => $column) {
                if ($request['data-' . $column->id] !== null) {
                    $inputExists = true;
                    break;
                }
            }

            if (!$inputExists) {
                return back()
                    ->withErrors(['input' => 'Data yang dimasukkan tidak boleh kosong semua']);
            }

            $ps = $ikp->programStrategis;
            $ikk = $ps->indikatorKinerjaKegiatan;
            $sk = $ikk->sasaranKegiatan;

            $year = $sk->time;

            $currentMonth = (int) Carbon::now()->format('m');
            $currentYear = Carbon::now()->format('Y');
            $currentPeriod = '1';

            foreach ([3, 6, 9, 12] as $key => $value) {
                if ($currentMonth <= $value) {
                    $temp = $key + 1;
                    $currentPeriod = "$temp";

                    break;
                }
            }

            $periodInstance = $year->periods()
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->where('period', $period)
                ->where('status', true)
                ->firstOrFail();

            $achievement = new IKUAchievement();

            $achievement->indikatorKinerjaProgram()->associate($ikp);
            $achievement->unit()->associate(auth()->user()->unit);
            $achievement->period()->associate($periodInstance);

            $achievement->save();

            $columns->where('file', false)->each(function ($column) use ($achievement, $request) {
                $input = $request['data-' . $column->id];
                if ($input !== null) {
                    $data = new IKUAchievementData();

                    $data->achievement()->associate($achievement);
                    $data->column()->associate($column);

                    $data->data = $input;

                    $data->save();
                }
            });

            $file = $columns->firstWhere('file', true);

            if ($file !== null) {
                $name = 'file-' . $file->id;
                if ($request->hasFile($name)) {
                    $fileURI = $request->file($name)
                        ->store('IKUFiles/' . auth()->user()->unit->name . '/' . $ikp->id);

                    $data = new IKUAchievementData();

                    $data->achievement()->associate($achievement);
                    $data->column()->associate($file);

                    $data->data = $fileURI;

                    $data->save();
                }
            }

            $evaluation = $ikp->evaluation;

            if ($evaluation) {
                $all = $ikp->achievements()
                    ->count();

                $evaluation->status = $all >= $evaluation->target;
                $evaluation->save();
            }

            return back();
        }

        abort(404);
    }

    public function delete(IndikatorKinerjaProgram $ikp, IKUAchievement $achievement)
    {
        if ($ikp->id === $achievement->indikatorKinerjaProgram->id && $ikp->status === 'aktif') {
            if ($achievement->period->status == 1 && $achievement->period->deadline !== null) {
                if ($achievement->unit->id === auth()->user()->unit->id) {
                    $achievement->data->each(function ($data) {
                        if ($data->column->file) {
                            if (Storage::exists($data->data)) {
                                Storage::delete($data->data);
                            }
                        }
                        $data->forceDelete();
                    });

                    $achievement->forceDelete();

                    $evaluation = $ikp->evaluation;
                    if ($evaluation) {
                        $all = $ikp->achievements()
                            ->count();

                        $evaluation->status = $all >= $evaluation->target;
                        $evaluation->save();
                    }

                    return back();
                }
            }
        }

        abort(404);
    }
}
