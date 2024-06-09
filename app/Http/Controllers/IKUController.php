<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndikatorKinerjaUtama\AddTargetRequest;
use App\Http\Requests\IndikatorKinerjaUtama\AddRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaKegiatan;
use App\Models\IndikatorKinerjaProgram;
use App\Models\IKUAchievementData;
use App\Models\ProgramStrategis;
use App\Models\SasaranKegiatan;
use Illuminate\Support\Carbon;
use App\Models\IKUAchievement;
use App\Models\IKUEvaluation;
use Illuminate\Http\Request;
use App\Models\IKPColumn;
use App\Models\IKUPeriod;
use App\Models\IKUTarget;
use App\Models\IKUYear;
use App\Models\Unit;

class IKUController extends Controller
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

        // $this->checkRoutine($currentYear);

        $years = IKUYear::orderBy('year')->pluck('year')->toArray();

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

        $periods = $yearInstance->periods
            ->select(['id', 'period', 'status']);

        $periods = $periods->map(function ($item) {
            $title = 'TW 1 | Jan - Mar';
            if ($item['period'] === '2') {
                $title = 'TW 2 | Apr - Jun';
            } else if ($item['period'] === '3') {
                $title = 'TW 3 | Jul - Sep';
            } else if ($item['period'] === '4') {
                $title = 'TW 4 | Okt - Des';
            }

            return [
                ...$item,
                'title' => $title,
            ];
        });

        $periods = $periods->sortBy('period')->toArray();

        $badge = [$year];

        return view('super-admin.achievement.iku.home', compact([
            'periods',
            'badge',
            'years',
            'year',
        ]));
    }

    public function targetView($year)
    {
        $yearInstance = IKUYear::where('year', $year)
            ->firstOrFail();

        $currentYear = Carbon::now()->format('Y');

        $data = $yearInstance->sasaranKegiatan()
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query) {
                    $query->orderBy('number')
                        ->select(['sasaran_kegiatan_id', 'name AS ikk', 'id']);
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                    $query->orderBy('number')
                        ->select(['indikator_kinerja_kegiatan_id', 'name AS ps', 'id'])
                        ->withCount('indikatorKinerjaProgram AS rowspan');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->orderBy('number')
                        ->select(['program_strategis_id', 'name AS ikp', 'definition', 'type', 'id'])
                        ->with('target', function (HasMany $query) {
                            $query->select(['indikator_kinerja_program_id', 'unit_id', 'target', 'id']);
                        });
                },
            ])
            ->orderBy('number')
            ->select(['id', 'number', 'name AS sk'])
            ->get();

        $data = $data->map(function ($item) {
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
        });

        $data = $data->toArray();

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
            ->select(['short_name', 'name', 'id'])
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

    public function addTarget(AddTargetRequest $request, $ikpId, $unitId)
    {
        $ikp = IndikatorKinerjaProgram::findOrFail($ikpId);
        $unit = Unit::withTrashed()->findOrFail($unitId);

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

        $sumAllTarget = IKUTarget::whereBelongsTo($ikp)
            ->sum('target');

        $evaluation = IKUEvaluation::firstOrNew([
            'indikator_kinerja_program_id' => $ikpId
        ]);

        $evaluation->target = $sumAllTarget;

        $realization = IKUAchievement::whereBelongsTo($ikp)
            ->count();

        $evaluation->status = false;
        if ($realization >= $sumAllTarget) {
            $evaluation->status = true;
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
                ->pluck('period');


            $periods = $periods->map(function ($item) {
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
            $periodInstance = IKUPeriod::where('status', true)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
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
                            ->select(['sasaran_kegiatan_id', 'name AS ikk', 'id']);
                    },
                    'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                        $query->whereHas('indikatorKinerjaProgram', function (Builder $query) {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select(['indikator_kinerja_kegiatan_id', 'name AS ps', 'id'])
                            ->withCount([
                                'indikatorKinerjaProgram AS rowspan' => function (Builder $query) {
                                    $query->where('status', 'aktif');
                                }
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select(['program_strategis_id', 'name AS ikp', 'definition', 'type', 'id'])
                            ->withCount([
                                'achievements AS achievements' => function (Builder $query) {
                                    $query->whereBelongsTo(auth()->user()->unit);
                                }
                            ]);
                    },
                ])
                ->orderBy('number')
                ->select(['id', 'number', 'name AS sk'])
                ->get();

            $data = $data->map(function ($item) {
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
            });

            $data = $data->toArray();

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

    public function detailViewAdmin(Request $request, $ikpId)
    {
        if (isset($request->period) && !in_array($request->period, ['1', '2', '3', '4'])) {
            abort(404);
        }

        $ikp = IndikatorKinerjaProgram::with('columns')
            ->whereKey($ikpId)
            ->where('status', 'aktif')
            ->firstOrFail();

        $ps = ProgramStrategis::findOrFail($ikp->programStrategis->id);
        $ikk = IndikatorKinerjaKegiatan::findOrFail($ps->indikatorKinerjaKegiatan->id);
        $sk = SasaranKegiatan::findOrFail($ikk->sasaranKegiatan->id);
        $year = IKUYear::findOrFail($sk->time->id);

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
            ->pluck('period');

        if (!count($periods)) {
            abort(404);
        }

        $periods = $periods->map(function ($item) {
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
        $periodInstance = IKUPeriod::where('status', true)
            ->where('year_id', $year->id)
            ->where('period', $period)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->firstOrFail();

        $columns = $ikp->columns()
            ->select(['file', 'name', 'id'])
            ->orderBy('number')
            ->get()
            ->toArray();

        $data = $periodInstance->achievements()
            ->with('data', function (HasMany $query) {
                $query->select(['achievement_id', 'column_id', 'data'])
                    ->withAggregate('column AS file', 'file');
            })
            ->whereBelongsTo(auth()->user()->unit)
            ->whereBelongsTo($ikp)
            ->select('id')
            ->latest()
            ->get()
            ->toArray();

        $sk = $sk->only(['number', 'name', 'id']);
        $ikk = $ikk->only(['number', 'name', 'id']);
        $ps = $ps->only(['number', 'name', 'id']);
        $ikp = $ikp->only(['number', 'name', 'id']);

        $badge = [$periods->firstWhere('value', $period)['title'], $year->year];
        $periods = $periods->toArray();

        return view('admin.iku.detail', compact([
            'columns',
            'periods',
            'period',
            'badge',
            'data',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }

    public function addData(AddRequest $request, $period, $ikpId)
    {
        $ikp = IndikatorKinerjaProgram::whereKey($ikpId)
            ->where('status', 'aktif')
            ->firstOrFail();

        $columns = IKPColumn::whereBelongsTo($ikp)
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

        $ps = ProgramStrategis::findOrFail($ikp->programStrategis->id);
        $ikk = IndikatorKinerjaKegiatan::findOrFail($ps->indikatorKinerjaKegiatan->id);
        $sk = SasaranKegiatan::findOrFail($ikk->sasaranKegiatan->id);
        $year = IKUYear::findOrFail($sk->time->id);

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

        $periodInstance = IKUPeriod::whereBelongsTo($year, 'year')
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
                $fileURI = $request->file($name)->store($ikp->id);

                $data = new IKUAchievementData();

                $data->achievement()->associate($achievement);
                $data->column()->associate($file);

                $data->data = $fileURI;

                $data->save();
            }
        }

        return back();
    }
}
