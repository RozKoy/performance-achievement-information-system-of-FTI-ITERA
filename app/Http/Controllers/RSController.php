<?php

namespace App\Http\Controllers;

use App\Http\Requests\RencanaStrategis\AddEvaluationRequest;
use App\Http\Requests\RencanaStrategis\AddTargetRequest;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Requests\RencanaStrategis\ImportRequest;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Requests\RencanaStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Imports\RencanaStrategisSheets;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;
use App\Models\IndikatorKinerja;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use App\Models\RSAchievement;
use Illuminate\Http\Request;
use App\Exports\RSExport;
use App\Models\Kegiatan;
use App\Models\RSPeriod;
use App\Models\RSYear;
use App\Models\Unit;

class RSController extends Controller
{
    /*
    | -----------------------------------------------------------------
    | SUPER ADMIN
    | -----------------------------------------------------------------
    */

    /**
     * RS home view 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeView(Request $request): Factory|View
    {
        if (isset($request->year) && !is_numeric($request->year)) {
            abort(404);
        }
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3'])) {
            abort(404);
        }

        $system = true;

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

        $currentYearInstance = RSYear::currentTime();

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $this->checkRoutine($currentYear, $currentPeriod);

        $years = RSYear::orderBy('year')
            ->pluck('year')
            ->toArray();

        $year = isset($request->year) ? $request->year : end($years);
        $yearInstance = RSYear::where('year', $year)->firstOrFail();

        $this->periodFirstOrNew($yearInstance->id, '1');
        if ($year !== $currentYear || $currentPeriod === '2') {
            $this->periodFirstOrNew($yearInstance->id, '2');
        }

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item): array {
                $title = 'Januari - Juni';

                if ($item === '2') {
                    $title = 'Juli - Desember';
                }

                return [
                    'title' => $title,
                    'value' => $item
                ];
            })
            ->toArray();

        if (count($periods) === 2) {
            $periods[] = [
                'title' => 'Januari - Desember',
                'value' => '3'
            ];
        }

        $period = $request->period ?? end($periods)['value'];

        if ((int) $period > count($periods)) {
            abort(404);
        }

        $periodInstance = null;
        if ($period !== '3') {
            $periodInstance = RSPeriod::where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->firstOrFail();

            $system = $periodInstance->status;
        }

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('indikatorKinerja')
            ->with([
                'kegiatan' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerja')
                        ->orderBy('number')
                        ->select([
                            'name AS k',
                            'number',
                            'id',

                            'sasaran_strategis_id',
                        ])
                        ->withCount('indikatorKinerja AS rowspan');
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query) use ($periodInstance): void {
                    $query->orderBy('number')
                        ->select([
                            'name AS ik',
                            'status',
                            'number',
                            'type',
                            'id',

                            'kegiatan_id',
                        ])
                        ->withAggregate([
                            'realization AS realization' => function (Builder $query) use ($periodInstance): void {
                                $query->whereNull('unit_id');
                                if ($periodInstance) {
                                    $query->whereBelongsTo($periodInstance, 'period');
                                } else {
                                    $query->whereNull('period_id');
                                }
                            }
                        ], 'realization')
                        ->withCount([
                            'realization AS count' => function (Builder $query) use ($periodInstance): void {
                                $query->whereNotNull('unit_id');
                                if ($periodInstance) {
                                    $query->whereBelongsTo($periodInstance, 'period');
                                } else {
                                    $query->whereNotNull('period_id');
                                }
                            }
                        ])
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS done', 'status');
                }
            ])
            ->orderBy('number')
            ->select([
                'name AS ss',
                'number',
                'id',
            ])
            ->withCount([
                'indikatorKinerja AS rowspan',
                'indikatorKinerja AS success' => function (Builder $query): void {
                    $query->whereHas('evaluation', function (Builder $query): void {
                        $query->where('status', true);
                    });
                },
                'indikatorKinerja AS failed' => function (Builder $query): void {
                    $query->whereDoesntHave('evaluation')
                        ->orWhereHas('evaluation', function (Builder $query): void {
                            $query->where('status', false);
                        });
                },
            ])
            ->get();

        $allCount = $data->sum('rowspan');

        $realizationCount = $data->sum(function (SasaranStrategis $ss): int {
            $sum = $ss->kegiatan->sum(function (Kegiatan $k): int {
                $sum = $k->indikatorKinerja->sum('count');
                return $sum;
            });
            return $sum;
        });

        $unitCount = Unit::where(function (Builder $query) use ($year): void {
            $query->whereNotNull('deleted_at')
                ->whereHas('rencanaStrategis', function (Builder $query) use ($year): void {
                    $query->whereHas('period', function (Builder $query) use ($year): void {
                        $query->whereHas('year', function (Builder $query) use ($year): void {
                            $query->where('year', $year);
                        });
                    });
                });
        })
            ->orWhereNull('deleted_at')
            ->withTrashed()
            ->count();

        $unitCount *= $periodInstance === null ? 2 : 1;

        $success = $data->sum('success');
        $failed = $data->sum('failed');

        $badge = [
            $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni'),
            $year
        ];

        $periodId = $periodInstance?->id;
        $data = $data->toArray();

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

    /**
     * RS detail view 
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerja $ik
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function detailView(Request $request, IndikatorKinerja $ik): Factory|View
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

        if (isset($request->period) && !in_array($request->period, ['1', '2', '3'])) {
            abort(404);
        }

        $k = $ik->kegiatan;
        $ss = $k->sasaranStrategis;

        $yearInstance = $ss->time;
        $year = $yearInstance->year;

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item): array {
                $title = 'Januari - Juni';

                if ($item === '2') {
                    $title = 'Juli - Desember';
                }

                return [
                    'title' => $title,
                    'value' => $item
                ];
            });

        if ($periods->count() === 2) {
            $periods->push([
                'title' => 'Januari - Desember',
                'value' => '3'
            ]);
        }

        $period = isset($request->period) ? $request->period : $periods->last()['value'];

        if ((int) $period > $periods->count()) {
            abort(404);
        }
        $periods = $periods->toArray();

        $periodInstance = $yearInstance->periods()
            ->where('period', $period)
            ->first();

        $data = $ik->realization()
            ->with('unit', function (BelongsTo $query) use ($ik): void {
                $query->withTrashed()
                    ->select([
                        'name',
                        'id',
                    ])
                    ->withAggregate([
                        'rencanaStrategisTarget AS target' => function (Builder $query) use ($ik): void {
                            $query->whereBelongsTo($ik);
                        }
                    ], 'target');
            })
            ->where(function (Builder $query) use ($periodInstance): void {
                $query->whereNotNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNull('period_id');
                }
            })
            ->select([
                'unit_id',

                'realization',
                'link',
            ])
            ->latest()
            ->get()
            ->toArray();

        $realization = $ik->realization()
            ->where(function (Builder $query) use ($periodInstance): void {
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

            $evaluation = $evaluation->only([
                'evaluation',
                'follow_up',
                'status',
                'target',
            ]);
        }

        $unitCount = Unit::where(function (Builder $query) use ($year): void {
            $query->whereNotNull('deleted_at')
                ->whereHas('rencanaStrategis', function (Builder $query) use ($year): void {
                    $query->whereHas('period', function (Builder $query) use ($year): void {
                        $query->whereHas('year', function (Builder $query) use ($year): void {
                            $query->where('year', $year);
                        });
                    });
                });
        })
            ->orWhereNull('deleted_at')
            ->withTrashed()
            ->count();

        $unitCount *= $periodInstance === null ? 2 : 1;

        $realizationCount = $ik->realization()
            ->where(function (Builder $query) use ($periodInstance): void {
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

        $ss = $ss->only([
            'number',
            'name',
        ]);
        $k = $k->only([
            'number',
            'name',
        ]);
        $ik = $ik->only([
            'number',
            'status',
            'name',
            'type',
            'id',
        ]);

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

    /**
     * RS target view 
     * @param mixed $year
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function targetView($year): Factory|View
    {
        $yearInstance = RSYear::where('year', $year)
            ->firstOrFail();

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('indikatorKinerja', function (Builder $query): void {
                $query->where('status', 'aktif')
                    ->whereNot('type', 'teks');
            })
            ->with([
                'kegiatan' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerja', function (Builder $query): void {
                        $query->where('status', 'aktif')
                            ->whereNot('type', 'teks');
                    })
                        ->orderBy('number')
                        ->select([
                            'name AS k',
                            'number',
                            'id',

                            'sasaran_strategis_id',
                        ])
                        ->withCount([
                            'indikatorKinerja AS rowspan' => function (Builder $query): void {
                                $query->where('status', 'aktif')
                                    ->whereNot('type', 'teks');
                            }
                        ]);
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query): void {
                    $query->where('status', 'aktif')
                        ->whereNot('type', 'teks')
                        ->orderBy('number')
                        ->select([
                            'name AS ik',
                            'number',
                            'status',
                            'type',
                            'id',

                            'kegiatan_id',
                        ])
                        ->withAggregate('evaluation AS all_target', 'target')
                        ->with('target', function (HasMany $query): void {
                            $query->select([
                                'target',
                                'id',

                                'indikator_kinerja_id',
                                'unit_id',
                            ]);
                        });
                }
            ])
            ->orderBy('number')
            ->select([
                'name AS ss',
                'number',
                'id',
            ])
            ->withCount([
                'indikatorKinerja AS rowspan' => function (Builder $query): void {
                    $query->where('status', 'aktif')
                        ->whereNot('type', 'teks');
                }
            ])
            ->get()
            ->toArray();

        $units = Unit::where(function (Builder $query) use ($year): void {
            $query->whereNotNull('deleted_at')
                ->whereHas('rencanaStrategis', function (Builder $query) use ($year): void {
                    $query->whereHas('period', function (Builder $query) use ($year): void {
                        $query->whereHas('year', function (Builder $query) use ($year): void {
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

        return view('super-admin.achievement.rs.target', compact([
            'units',
            'data',
            'year',
        ]));
    }

    /**
     * period first or new function 
     * @param mixed $yearId
     * @param mixed $value
     * @return void
     */
    public function periodFirstOrNew($yearId, $value): void
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

    /**
     * Check routine function 
     * @param mixed $currentYear
     * @param mixed $currentPeriod
     * @return void
     */
    public function checkRoutine($currentYear, $currentPeriod): void
    {
        $currentPeriod = RSPeriod::where('period', $currentPeriod)
            ->whereHas('year', function (Builder $query) use ($currentYear): void {
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

    /**
     * RS status toggle function 
     * @param \App\Models\RSPeriod $period
     * @return \Illuminate\Http\RedirectResponse
     */
    public function statusToggle(RSPeriod $period): RedirectResponse
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

        return back();
    }

    /**
     * RS add evaluation function 
     * @param \App\Http\Requests\RencanaStrategis\AddEvaluationRequest $request
     * @param \App\Models\IndikatorKinerja $ik
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addEvaluation(AddEvaluationRequest $request, IndikatorKinerja $ik): RedirectResponse
    {
        if ($request['period'] === '3') {
            if ($request['target'] === null && ($ik->type === 'teks' || $ik->status !== 'aktif')) {
                return back()->withErrors(['target' => 'Target wajib diisi']);
            } else if ($ik->type !== 'teks' && $ik->status !== 'aktif' && !is_numeric($request['target'])) {
                return back()->withErrors(['target' => 'Target harus berupa angka']);
            }

            if ($ik->type === 'teks' && $request['status'] === null) {
                return back()->withErrors(['status' => 'Status wajib diisi']);
            }
        } else if ($ik->status === 'tidak aktif' && $ik->type !== 'teks' && $request['realization'] !== null) {
            if (!is_numeric($request['realization'])) {
                return back()->withErrors(['realization' => 'Realisasi harus berupa angka']);
            } else if ((float) $request['realization'] < 0) {
                $request['realization'] *= -1;
                $request['realization'] = "{$request['realization']}";
            }
        }

        $k = $ik->kegiatan;
        $ss = $k->sasaranStrategis;

        $yearInstance = $ss->time;
        $periodInstance = $yearInstance->periods()
            ->where('period', $request['period'])
            ->first();

        if ($ik->type === 'teks' || ($ik->type !== 'teks' && $ik->status === 'tidak aktif' && $periodInstance !== null)) {
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

            if ($ik->type !== 'teks') {
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
                if ($ik->type === 'persen') {
                    $count = $all->count();
                    $final = $count ? $final / $count : 0;
                }

                $temp->realization = $final;
                $temp->save();
            }
        }

        if ($request['period'] === '3') {
            $evaluation = $ik->evaluation()->firstOrNew();

            $target = 0;
            if ($ik->type !== 'teks' && $ik->status === 'aktif') {
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

        if ($ik->type !== 'teks') {
            $evaluation = $ik->evaluation;

            if ($evaluation) {
                $realization = $ik->realization()
                    ->whereNull(['period_id', 'unit_id'])
                    ->first();

                if ($realization) {
                    $evaluation->status = (float) $realization->realization >= (float) $evaluation->target;
                } else {
                    $evaluation->status = false;
                }

                $evaluation->save();
            }
        }

        return back();
    }

    /**
     * RS add target
     * @param \App\Http\Requests\RencanaStrategis\AddTargetRequest $request
     * @param mixed $year
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addTarget(AddTargetRequest $request, $year): RedirectResponse
    {
        $targets = $request['target'];

        $indikatorKinerja = IndikatorKinerja::where('status', 'aktif')
            ->whereNot('type', 'teks')
            ->whereHas('kegiatan', function (Builder $query) use ($year): void {
                $query->whereHas('sasaranStrategis', function (Builder $query) use ($year): void {
                    $query->whereHas('time', function (Builder $query) use ($year): void {
                        $query->where('year', $year);
                    });
                });
            })
            ->get();

        $units = Unit::where(function (Builder $query) use ($year): void {
            $query->whereNotNull('deleted_at')
                ->whereHas('rencanaStrategis', function (Builder $query) use ($year): void {
                    $query->whereHas('period', function (Builder $query) use ($year): void {
                        $query->whereHas('year', function (Builder $query) use ($year): void {
                            $query->where('year', $year);
                        });
                    });
                });
        })
            ->orWhereNull('deleted_at')
            ->withTrashed()
            ->get();

        foreach ($indikatorKinerja as $ik) {
            foreach ($units as $unit) {
                if ($targets["$ik->id-$unit->id"] ?? null !== null) {
                    $temp = $ik->target()->firstOrNew(
                        [
                            'unit_id' => $unit->id,
                        ],
                        [
                            'unit_id' => $unit->id,
                        ],
                    );

                    $temp->target = $targets["$ik->id-$unit->id"];

                    $temp->save();
                } else {
                    $ik->target()->where('unit_id', $unit->id)->forceDelete();
                }
            }
            $newTarget = $ik->type === 'angka' ? $ik->target()->sum('target') : $ik->target()->average('target');

            if ($newTarget) {
                $realization = $ik->realization()->whereNull(['period_id', 'unit_id'])->first();
                $realization = $realization !== null ? (float) $realization->realization : $newTarget - 1;

                $evaluation = $ik->evaluation()->firstOrNew();

                $evaluation->target = $newTarget;
                $evaluation->status = $realization >= $newTarget;

                $evaluation->save();
            } else {
                $ik->evaluation()->forceDelete();
            }
        }

        return back();
    }

    /**
     * RS excel import function 
     * @param \App\Http\Requests\RencanaStrategis\ImportRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function RSImport(ImportRequest $request): RedirectResponse
    {
        Excel::import(
            new RencanaStrategisSheets,
            $request->file('file')
        );

        return back();
    }

    /**
     * RS excel download function 
     * @param \Illuminate\Http\Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportRS(Request $request): BinaryFileResponse
    {
        if (isset($request->year) && !is_numeric($request->year)) {
            abort(404);
        }
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3'])) {
            abort(404);
        }

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $years = RSYear::orderBy('year')
            ->pluck('year')
            ->toArray();

        $year = isset($request->year) ? $request->year : end($years);
        $yearInstance = RSYear::where('year', $year)->firstOrFail();

        $this->periodFirstOrNew($yearInstance->id, '1');
        if ($year !== $currentYear || $currentPeriod === '2') {
            $this->periodFirstOrNew($yearInstance->id, '2');
        }

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->toArray();

        if (count($periods) === 2) {
            $periods[] = '3';
        }

        $period = $request->period ?? end($periods);

        if ((int) $period > count($periods)) {
            abort(404);
        }

        $periodInstance = null;
        if ($period !== '3') {
            $periodInstance = RSPeriod::where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->firstOrFail();
        }

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('indikatorKinerja')
            ->with([
                'kegiatan' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerja')
                        ->orderBy('number')
                        ->select([
                            'name',
                            'id',

                            'sasaran_strategis_id',
                        ]);
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query) use ($periodInstance): void {
                    $query->orderBy('number')
                        ->select([
                            'name',
                            'id',

                            'kegiatan_id',
                        ])
                        ->withAggregate([
                            'realization AS realization' => function (Builder $query) use ($periodInstance): void {
                                $query->whereNull('unit_id');
                                if ($periodInstance) {
                                    $query->whereBelongsTo($periodInstance, 'period');
                                } else {
                                    $query->whereNull('period_id');
                                }
                            }
                        ], 'realization')
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS status', 'status');
                }
            ])
            ->orderBy('number')
            ->select([
                'number',
                'name',
                'id',
            ])
            ->withCount([
                'indikatorKinerja AS success' => function (Builder $query): void {
                    $query->whereHas('evaluation', function (Builder $query): void {
                        $query->where('status', true);
                    });
                },
                'indikatorKinerja AS failed' => function (Builder $query): void {
                    $query->whereDoesntHave('evaluation')
                        ->orWhereHas('evaluation', function (Builder $query): void {
                            $query->where('status', false);
                        });
                },
            ])
            ->get();

        $success = $data->sum('success');
        $failed = $data->sum('failed');

        $collection = collect([
            ['Tahun', $year],
            ['Periode', $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni')],
        ]);

        if ($period === '3') {
            $collection->add(['Tercapai', $success, 'Tidak Tercapai', $failed]);
            $collection->add([
                'no',
                'sasaran strategis',
                'kegiatan',
                'indikator kinerja',
                'realisasi',
                "target $year",
                "evaluasi",
                "tindak lanjut",
                "status",
            ]);
        } else {
            $collection->add([
                'no',
                'sasaran strategis',
                'kegiatan',
                'indikator kinerja',
                'realisasi',
            ]);
        }

        $data->each(function ($ss) use ($collection, $period): void {
            $ss->kegiatan->each(function ($k, $kIndex) use ($collection, $period, $ss): void {
                $k->indikatorKinerja->each(function ($ik, $ikIndex) use ($collection, $kIndex, $period, $ss, $k): void {
                    $temp = ['', '', '', '', '', '', '', '', ''];
                    if (!$ikIndex) {
                        if (!$kIndex) {
                            $temp[0] = $ss->number;
                            $temp[1] = $ss->name;
                        }
                        $temp[2] = $k->name;
                    }
                    $temp[3] = $ik->name;
                    $temp[4] = $ik->realization;

                    if ($period === '3') {
                        $temp[5] = $ik->target;
                        $temp[6] = $ik->evaluation;
                        $temp[7] = $ik->follow_up;
                        $temp[8] = $ik->status ? 'Tercapai' : 'Tidak tercapai';
                    }

                    $collection->add($temp);
                });
            });
        });

        return Excel::download(new RSExport($collection->toArray()), 'rencana-strategis.xlsx');
    }


    /*
    | -----------------------------------------------------------------
    | ADMIN
    | -----------------------------------------------------------------
    */

    protected $adminStatus = [
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

    /**
     * RS admin home view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function homeViewAdmin(Request $request): Factory|View
    {
        $status = $this->adminStatus;

        if ($request->year !== null && !is_numeric($request->year)) {
            abort(404);
        }
        if ($request->period !== null && !in_array($request->period, ['1', '2'])) {
            abort(404);
        }

        $periods = [];

        $period = '';
        $year = '';

        $badge = [];
        $data = [];

        $periodId = '';

        $doneCount = 0;
        $allCount = 0;

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
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear): void {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear): void {
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
            $year = $request->year ?? end($years);
            $yearInstance = RSYear::where('year', $year)->firstOrFail();

            $periods = $yearInstance->periods()
                ->where('status', true)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear): void {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear): void {
                            $query->where('year', $currentYear);
                        });
                })
                ->orderBy('period')
                ->pluck('period')
                ->map(function ($item): array {
                    return [
                        'title' => $item === '1' ? 'Januari - Juni' : 'Juli - Desember',
                        'value' => $item
                    ];
                })
                ->toArray();

            if (!count($periods)) {
                abort(404);
            }

            $period = $request->period ?? end($periods)['value'];

            $periodInstance = RSPeriod::where('status', true)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear): void {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear): void {
                            $query->where('year', $currentYear);
                        });
                })
                ->firstOrFail();

            $data = $yearInstance->sasaranStrategis()
                ->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance): void {
                    $query->where('status', 'aktif');
                    if ($statusIndex === 1) {
                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    } else if ($statusIndex === 2) {
                        $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    }
                })
                ->with([
                    'kegiatan' => function (HasMany $query) use ($statusIndex, $periodInstance) {
                        $query->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance): void {
                            $query->where('status', 'aktif');
                            if ($statusIndex === 1) {
                                $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            } else if ($statusIndex === 2) {
                                $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            }
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS k',
                                'number',
                                'id',

                                'sasaran_strategis_id',
                            ])
                            ->withCount([
                                'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance): void {
                                    $query->where('status', 'aktif');
                                    if ($statusIndex === 1) {
                                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                            $query->whereBelongsTo(auth()->user()->unit)
                                                ->whereBelongsTo($periodInstance, 'period');
                                        });
                                    } else if ($statusIndex === 2) {
                                        $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                                            $query->whereBelongsTo(auth()->user()->unit)
                                                ->whereBelongsTo($periodInstance, 'period');
                                        });
                                    }
                                }
                            ]);
                    },
                    'kegiatan.indikatorKinerja' => function (HasMany $query) use ($statusIndex, $periodInstance): void {
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        }
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select([
                                'name AS ik',
                                'number',
                                'type',
                                'id',

                                'kegiatan_id',
                            ])
                            ->withAggregate([
                                'realization AS yearRealization' => function (Builder $query): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereDoesntHave('period');
                                }
                            ], 'realization')
                            ->withAggregate([
                                'realization AS realization' => function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'realization')
                            ->withAggregate([
                                'realization AS link' => function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'link')
                            ->withAggregate([
                                'target AS target' => function (Builder $query): void {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                }
                            ], 'target');
                    }
                ])
                ->orderBy('number')
                ->select([
                    'name AS ss',
                    'number',
                    'id',
                ])
                ->withCount([
                    'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance): void {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
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
                    'indikatorKinerja AS all' => function (Builder $query): void {
                        $query->where('status', 'aktif');
                    },
                    'indikatorKinerja AS done' => function (Builder $query) use ($periodInstance): void {
                        $query->where('status', 'aktif')
                            ->whereHas('realization', function (Builder $query) use ($periodInstance): void {
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
            'data',
        ]));
    }

    /**
     * RS admin add function
     * @param \App\Http\Requests\RencanaStrategis\AddRequest $request
     * @param string $periodId
     * @param string $ikId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addAdmin(AddRequest $request, string $periodId, string $ikId): RedirectResponse
    {
        $realization = $request["realization-$ikId"];
        $link = $request["link-$ikId"];

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $period = RSPeriod::whereKey($periodId)
            ->where('status', true)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear): void {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear): void {
                        $query->where('year', $currentYear);
                    });
            })
            ->firstOrFail();

        $ik = IndikatorKinerja::whereKey($ikId)
            ->where('status', 'aktif')
            ->whereHas('kegiatan', function (Builder $query) use ($period): void {
                $query->whereHas('sasaranStrategis', function (Builder $query) use ($period): void {
                    $query->whereBelongsTo($period->year, 'time');
                });
            })
            ->firstOrFail();


        if ($realization !== null && $link === null) {
            return _ControllerHelpers::BackWithInputWithErrors(["link-$ikId" => 'Link bukti wajib diisi']);
        }

        if ($realization !== null && !is_numeric($realization) && ($ik->type === 'persen' || $ik->type === 'angka')) {
            return _ControllerHelpers::BackWithInputWithErrors(["realization-$ikId" => 'Realisasi tidak sesuai dengan tipe data']);
        }

        if ($ik->type !== 'teks' && $realization !== null) {
            $realization = (float) $realization;
            if ($realization < 0) {
                $realization *= -1;
            }
            if (!ctype_digit((string) $realization)) {
                $realization = number_format($realization, 2);
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

            $achievement->realization = (string) $realization;
            $achievement->link = (string) $link;

            $achievement->unit()->associate(auth()->user()->unit);
            $achievement->indikatorKinerja()->associate($ik);
            $achievement->period()->associate($period);

            $achievement->save();
        } else {
            $achievement->forceDelete();
        }

        if ($ik->type !== 'teks') {
            foreach ([$allAchievement, $periodAchievement, $unitAchievement] as $instance) {
                $all = RSAchievement::whereBelongsTo($ik)
                    ->where(function (Builder $query) use ($instance): void {
                        if ($instance->period) {
                            $query->whereBelongsTo($instance->period, 'period')
                                ->whereNotNull('unit_id');
                        } else if ($instance->unit) {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereNotNull('period_id');
                        } else {
                            $query->whereNotNull('period_id')
                                ->whereNotNull('unit_id');
                        }
                    })
                    ->get();

                $sum = $ik->type === 'angka' ? $all->sum('realization') : $all->average('realization');

                if (!ctype_digit((string) $sum)) {
                    $sum = number_format($sum, 2);
                }

                $instance->realization = (string) $sum;
                $instance->save();
            }

            $evaluation = $ik->evaluation;

            if ($evaluation) {
                $evaluation->status = (float) $allAchievement->realization >= (float) $evaluation->target;
                $evaluation->save();
            }
        }

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbaharui data');
    }

    /**
     * RS admin history view
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function historyAdmin(Request $request): Factory|View
    {
        $status = $this->adminStatus;

        if ($request->year !== null && !is_numeric($request->year)) {
            abort(404);
        }
        if ($request->period !== null && !in_array($request->period, ['1', '2'])) {
            abort(404);
        }

        $periods = [];

        $period = '';
        $year = '';

        $badge = [];
        $data = [];

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
            $year = $request->year ?? end($years);
            $yearInstance = RSYear::where('year', $year)->firstOrFail();

            $periods = $yearInstance->periods()
                ->where('status', false)
                ->orderBy('period')
                ->pluck('period')
                ->flatten()
                ->unique()
                ->map(function ($item): array {
                    return [
                        'title' => $item === '1' ? 'Januari - Juni' : 'Juli - Desember',
                        'value' => $item
                    ];
                })
                ->toArray();

            if (!count($periods)) {
                abort(404);
            }

            $period = $request->period ?? end($periods)['value'];

            $periodInstance = RSPeriod::where('status', false)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->firstOrFail();

            $data = $yearInstance->sasaranStrategis()
                ->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance): void {
                    $query->where('status', 'aktif');
                    if ($statusIndex === 1) {
                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    } else if ($statusIndex === 2) {
                        $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                            $query->whereBelongsTo(auth()->user()->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    }
                })
                ->with([
                    'kegiatan' => function (HasMany $query) use ($statusIndex, $periodInstance): void {
                        $query->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance): void {
                            $query->where('status', 'aktif');
                            if ($statusIndex === 1) {
                                $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            } else if ($statusIndex === 2) {
                                $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            }
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS k',
                                'number',
                                'id',

                                'sasaran_strategis_id',
                            ])
                            ->withCount([
                                'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance): void {
                                    $query->where('status', 'aktif');
                                    if ($statusIndex === 1) {
                                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                            $query->whereBelongsTo(auth()->user()->unit)
                                                ->whereBelongsTo($periodInstance, 'period');
                                        });
                                    } else if ($statusIndex === 2) {
                                        $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                                            $query->whereBelongsTo(auth()->user()->unit)
                                                ->whereBelongsTo($periodInstance, 'period');
                                        });
                                    }
                                }
                            ]);
                    },
                    'kegiatan.indikatorKinerja' => function (HasMany $query) use ($statusIndex, $periodInstance): void {
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        }
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select([
                                'name AS ik',
                                'number',
                                'type',
                                'id',

                                'kegiatan_id',
                            ])
                            ->withAggregate([
                                'realization AS yearRealization' => function (Builder $query): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereDoesntHave('period');
                                }
                            ], 'realization')
                            ->withAggregate([
                                'realization AS realization' => function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'realization')
                            ->withAggregate([
                                'realization AS link' => function (Builder $query) use ($periodInstance): void {
                                    $query->whereBelongsTo(auth()->user()->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'link')
                            ->withAggregate([
                                'target AS target' => function (Builder $query): void {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                }
                            ], 'target');
                    }
                ])
                ->orderBy('number')
                ->select([
                    'name AS ss',
                    'number',
                    'id',
                ])
                ->withCount([
                    'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance): void {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance): void {
                                $query->whereBelongsTo(auth()->user()->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance): void {
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
