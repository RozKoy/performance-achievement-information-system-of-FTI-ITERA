<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaUtama\AddEvaluationRequest;
use App\Http\Requests\IndikatorKinerjaUtama\AddSingleDataRequest;
use App\Http\Requests\IndikatorKinerjaUtama\AddTableDataRequest;
use App\Http\Requests\IndikatorKinerjaUtama\ValidationRequest;
use App\Http\Requests\IndikatorKinerjaUtama\AddTargetRequest;
use App\Http\Requests\IndikatorKinerjaUtama\ImportRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Imports\IndikatorKinerjaUtamaSheets;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Models\IKUSingleAchievement;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IKPTableDataSheets;
use Illuminate\Contracts\View\View;
use App\Models\IKUAchievementData;
use Illuminate\Http\UploadedFile;
use App\Models\IKUAchievement;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\IKUExport;
use App\Http\Requests\IndikatorKinerjaUtama\SetDeadlineRequest;
use App\Models\IKUPeriod;
use App\Models\IKUYear;
use App\Models\Unit;

class IKUController extends Controller
{
    /*
    | -----------------------------------------------------------------
    | SUPER ADMIN
    | -----------------------------------------------------------------
    */

    /**
     * IKU home view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request): Factory|View
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
                'deadline',
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
                        'mode',
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
                        ->withAvg([
                            'singleAchievements AS tw1Single' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '1');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS tw2Single' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '2');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS tw3Single' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '3');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS tw4Single' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '4');
                                });
                            }
                        ], 'value')
                        ->withAvg('singleAchievements AS allSingle', 'value')
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

    /**
     * IKU detail view 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function detailView(Request $request, IndikatorKinerjaProgram $ikp): Factory|View
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

        $achievement = 0;
        $data = collect();

        if ($ikp->mode === 'table') {
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
                ->select([
                    'status',
                    'note',
                    'id',
                ])
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();

            $achievement = $data->count();
        } else {
            $data = IKUSingleAchievement::withTrashed()
                ->where(function (Builder $query) use ($periodInstance) {
                    if ($periodInstance) {
                        $query->whereBelongsTo($periodInstance, 'period');
                    }
                })
                ->whereBelongsTo($ikp)
                ->select([
                    'value',
                    'link',
                    'id',
                ])
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();

            $achievement = $data->average('value');
        }

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
            'mode',
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
            'achievement',
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

    /**
     * IKU target view 
     * @param mixed $year
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function targetView($year): Factory|View
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

    /**
     * Period first or new function
     * @param mixed $yearId
     * @param mixed $value
     * @return void
     */
    public function periodFirstOrNew($yearId, $value): void
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

    /**
     * Check routine function 
     * @param mixed $currentYear
     * @param mixed $currentPeriod
     * @return void
     */
    public function checkRoutine($currentYear, $currentPeriod): void
    {
        IKUPeriod::whereDate('deadline', '<', Carbon::now())
            ->update([
                'deadline' => null,
                'status' => false,
            ]);
    }

    /**
     * IKU status toggle function 
     * @param \App\Models\IKUPeriod $period
     * @return \Illuminate\Http\RedirectResponse
     */
    public function statusToggle(IKUPeriod $period): RedirectResponse
    {
        $deadline = null;
        $status = false;

        if (!$period->status) {
            $currentMonth = (int) Carbon::now()->format('m');
            $deadline = Carbon::now();

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

        return back();
    }

    public function setDeadline(SetDeadlineRequest $request, IKUPeriod $period): RedirectResponse
    {
        [
            "$period->id-deadline" => $deadline,
        ] = $request;

        if ($period->status) {
            $period->update([
                'deadline' => $deadline,
            ]);
        }

        return back();
    }

    /**
     * IKU add evaluation fuction 
     * @param \App\Http\Requests\IndikatorKinerjaUtama\AddEvaluationRequest $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addEvaluation(AddEvaluationRequest $request, IndikatorKinerjaProgram $ikp): RedirectResponse
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

    /**
     * IKU add target
     * @param \App\Http\Requests\IndikatorKinerjaUtama\AddTargetRequest $request
     * @param mixed $year
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTarget(AddTargetRequest $request, $year): RedirectResponse
    {
        $targets = $request['target'];

        $indikatorKinerjaProgram = IndikatorKinerjaProgram::where('status', 'aktif')
            ->whereHas('programStrategis', function (Builder $query) use ($year) {
                $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($year) {
                    $query->whereHas('sasaranKegiatan', function (Builder $query) use ($year) {
                        $query->whereHas('time', function (Builder $query) use ($year) {
                            $query->where('year', $year);
                        });
                    });
                });
            })
            ->get();

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
            ->withTrashed()
            ->get();

        foreach ($indikatorKinerjaProgram as $ikp) {
            foreach ($units as $unit) {
                if (isset($targets["$ikp->id-$unit->id"])) {
                    if ($targets["$ikp->id-$unit->id"] !== null) {
                        $temp = $ikp->target()->firstOrNew(
                            [
                                'unit_id' => $unit->id,
                            ],
                            [
                                'unit_id' => $unit->id,
                            ],
                        );

                        $temp->target = $targets["$ikp->id-$unit->id"];

                        $temp->save();

                        continue;
                    }
                }
                $ikp->target()->where('unit_id', $unit->id)->forceDelete();
            }
            $newTarget = $ikp->mode === 'table' ? $ikp->target()->sum('target') : number_format($ikp->target()->average('target'), 2);

            if ($newTarget) {
                $realization = $ikp->achievements()->count();
                if ($ikp->mode === 'single') {
                    $realization = $ikp->singleAchievements()->average('value') ?? $newTarget - 1;
                }

                $evaluation = $ikp->evaluation()->firstOrNew();

                $evaluation->target = $newTarget;
                $evaluation->status = $realization >= $newTarget;

                $evaluation->save();
            } else {
                $ikp->evaluation()->forceDelete();
            }
        }

        return back();
    }

    /**
     * Validation data
     * @param \App\Http\Requests\IndikatorKinerjaUtama\ValidationRequest $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function validation(ValidationRequest $request, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        [
            'data' => $data,
        ] = $request;

        foreach ($data ?? [] as $id => $item) {
            if ($achievement = $ikp->achievements()->find($id)) {
                try {
                    $achievement->note = $item['note'];
                } catch (\Exception $e) {
                }
                if (isset($item['status'])) {
                    $achievement->status = !$achievement->status;
                }
                $achievement->save();
            }
        }

        return back();
    }

    /**
     * IKU excel import function
     * @param \App\Http\Requests\IndikatorKinerjaUtama\ImportRequest $request
     * @return RedirectResponse
     */
    public function IKUImport(ImportRequest $request): RedirectResponse
    {
        Excel::import(
            new IndikatorKinerjaUtamaSheets,
            $request->file('file')
        );

        return back();
    }

    /**
     * IKU excel download function 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportIKU(Request $request): BinaryFileResponse
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
                $currentPeriod = (string) $temp;

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
                        'mode',
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
                        ->withAvg([
                            'singleAchievements AS singleTw1' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '1');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS singleTw2' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '2');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS singleTw3' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '3');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS singleTw4' => function (Builder $query) {
                                $query->whereHas('period', function (Builder $query) {
                                    $query->where('period', '4');
                                });
                            }
                        ], 'value')
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS status', 'status');
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

                        if ($ikp->mode === 'table') {
                            $temp[9] = $ikp->tw1;
                            $temp[10] = $ikp->tw2;
                            $temp[11] = $ikp->tw3;
                            $temp[12] = $ikp->tw4;
                        } else {
                            $temp[9] = $ikp->singleTw1;
                            $temp[10] = $ikp->singleTw2;
                            $temp[11] = $ikp->singleTw3;
                            $temp[12] = $ikp->singleTw4;
                        }

                        $temp[13] = $ikp->evaluation;
                        $temp[14] = $ikp->follow_up;
                        $temp[15] = $ikp->status ? 'Tercapai' : 'Tidak tercapai';

                        $collection->add($temp);
                    });
                });
            });
        });

        return Excel::download(new IKUExport($collection->toArray()), 'indikator-kinerja-utama.xlsx');
    }

    /**
     * IKU detail excel download function 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function detailExportIKU(Request $request, IndikatorKinerjaProgram $ikp): BinaryFileResponse
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

        $data = collect([]);
        if ($ikp->mode === 'table') {
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
        } else {
            $data = IKUSingleAchievement::withTrashed()
                ->where(function (Builder $query) use ($periodInstance) {
                    if ($periodInstance) {
                        $query->whereBelongsTo($periodInstance, 'period');
                    }
                })
                ->whereBelongsTo($ikp)
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();
        }

        $achievementCount = $ikp->mode === 'table' ? $data->count() : $data->average('value');
        $data = $data->groupBy('unit');

        $evaluation = $ikp->evaluation;

        $first = collect(['no']);
        if ($ikp->mode === 'table') {
            foreach ($columns as $column) {
                $first->add($column->name);
            }
        } else {
            $first->add('program studi');
            $first->add('realisasi');
            $first->add('bukti');
        }

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
            $first->toArray(),
        ]);

        if ($ikp->mode === 'table') {
            $data->each(function ($item, $key) use ($collection, $columns) {
                $collection->add([$key]);
                $item->each(function ($col, $index) use ($collection, $columns) {
                    $temp = collect([$index + 1]);

                    $columns->each(function ($column) use ($temp, $col) {
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
        } else {
            $index = 1;
            foreach ($data as $key => $item) {
                $collection->add([$index, $key, $item->average('value'), $item->count() === 1 ? $item->first()->link : '']);
                $index++;
            }
        }

        return Excel::download(new IKUExport($collection->toArray()), Str::replace(['/', '\\'], '-', (string) $ikp->name) . '.xlsx');
    }
}
