<?php

namespace App\Http\Controllers\Admin\IndikatorKinerjaUtama;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\IKUPeriod;
use App\Models\IKUYear;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Carbon;

class HistoryIndikatorKinerjaUtamaAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $periodQuery = $request->query('period');
        $yearQuery = $request->query('year');

        if ($yearQuery !== null && !is_numeric($yearQuery)) {
            abort(404);
        }
        if ($periodQuery !== null && !in_array($periodQuery, ['1', '2', '3', '4'])) {
            abort(404);
        }

        $user = auth()->user();

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
            $year = $yearQuery ?? end($years);
            $yearInstance = IKUYear::where('year', $year)->firstOrFail();

            $periods = $yearInstance->periods()
                ->where('status', false)
                ->orderBy('period')
                ->pluck('period')
                ->map(function ($item): array {
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

            $period = $periodQuery ?? $periods->last()['value'];
            $periodInstance = $yearInstance->periods()
                ->where('period', $period)
                ->where('status', false)
                ->firstOrFail();

            $data = $yearInstance->sasaranKegiatan()
                ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram', function (Builder $query): void {
                    $query->where('status', 'aktif');
                })
                ->with([
                    'indikatorKinerjaKegiatan' => function (HasMany $query): void {
                        $query->whereHas('programStrategis.indikatorKinerjaProgram', function (Builder $query): void {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS ikk',
                                'id',

                                'sasaran_kegiatan_id',
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query): void {
                        $query->whereHas('indikatorKinerjaProgram', function (Builder $query): void {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select([
                                'name AS ps',
                                'id',

                                'indikator_kinerja_kegiatan_id',
                            ])
                            ->withCount([
                                'indikatorKinerjaProgram AS rowspan' => function (Builder $query): void {
                                    $query->where('status', 'aktif');
                                }
                            ]);
                    },
                    'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) use ($periodInstance, $user): void {
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select([
                                'name AS ikp',
                                'definition',
                                'mode',
                                'type',
                                'id',

                                'program_strategis_id',
                            ])
                            ->withAggregate([
                                'target AS target' => function (Builder $query) use ($user): void {
                                    $query->whereBelongsTo($user->unit);
                                }
                            ], 'target')
                            ->withAggregate([
                                'singleAchievements AS valueSingle' => function (Builder $query) use ($periodInstance, $user): void {
                                    $query->whereBelongsTo($user->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'value')
                            ->withAggregate([
                                'singleAchievements AS linkSingle' => function (Builder $query) use ($periodInstance, $user): void {
                                    $query->whereBelongsTo($user->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'link')
                            ->withAvg([
                                'singleAchievements AS allSingle' => function (Builder $query) use ($user): void {
                                    $query->whereBelongsTo($user->unit);
                                }
                            ], 'value')
                            ->withCount([
                                'achievements AS all' => function (Builder $query) use ($user): void {
                                    $query->where('status', true)
                                        ->whereBelongsTo($user->unit);
                                },
                                'achievements AS achievements' => function (Builder $query) use ($periodInstance, $user): void {
                                    $query->where('status', true)
                                        ->whereBelongsTo($user->unit)
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
                ->map(function ($item): array {
                    $temp = $item->indikatorKinerjaKegiatan->map(function ($item): array {
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
            'user',
        ]));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return Factory|View
     */
    public function detailView(Request $request, IndikatorKinerjaProgram $ikp): Factory|View
    {
        $periodQuery = $request->query('period');

        if ($ikp->status !== 'aktif') {
            abort(404);
        }
        if ($periodQuery !== null && !in_array($periodQuery, ['1', '2', '3', '4'])) {
            abort(404);
        }

        $user = auth()->user();

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $periods = $year->periods()
            ->where('status', false)
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item): array {
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

        $period = $periodQuery ?? $periods->last()['value'];
        $periodInstance = $year->periods()
            ->where('period', $period)
            ->where('status', false)
            ->firstOrFail();

        $realization = null;
        $columns = [];
        $data = [];

        if ($ikp->mode === 'table') {
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
                ->with('data', function (HasMany $query): void {
                    $query->select([
                        'data',

                        'achievement_id',
                        'column_id',
                    ])
                        ->withAggregate('column AS file', 'file');
                })
                ->whereBelongsTo($user->unit)
                ->whereBelongsTo($ikp)
                ->select([
                    'status',
                    'note',
                    'id',
                ])
                ->orderBy('created_at')
                ->get()
                ->toArray();

            $realization = $ikp->achievements()
                ->where('status', true)
                ->whereBelongsTo($user->unit)
                ->count();
        } else {
            $data = $periodInstance->singleAchievements()
                ->whereBelongsTo($user->unit)
                ->whereBelongsTo($ikp)
                ->select([
                    'value',
                    'link',
                ])
                ->first()
                    ?->toArray();

            $realization = $ikp->singleAchievements()
                ->whereBelongsTo($user->unit)
                ->average('value');
        }

        $target = $ikp->target()
            ->whereBelongsTo($user->unit)
            ->first();
        if ($target) {
            $target = $target->target;
        }

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
            'mode',
            'name',
            'type',
            'id',
        ]);

        $badge = [$periods->firstWhere('value', $period)['title'], $year->year];
        $periods = $periods->toArray();

        return view('admin.history.iku.detail', compact([
            'realization',
            'columns',
            'periods',
            'period',
            'target',
            'badge',
            'data',
            'user',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }
}
