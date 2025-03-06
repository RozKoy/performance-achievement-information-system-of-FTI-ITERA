<?php

namespace App\Http\Controllers\Admin\RencanaStrategis;

use App\Http\Controllers\SuperAdmin\RencanaStrategis\HomeRencanaStrategisSuperAdminController;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSPeriod;
use App\Models\RSYear;

class HomeRencanaStrategisAdminController extends Controller
{
    public const ADMIN_STATUS = [
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
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        HomeRencanaStrategisSuperAdminController::CheckRoutine();

        $statusRequest = $request->query('status');
        $periodRequest = $request->query('period');
        $yearQuery = $request->query('year');

        if ($yearQuery !== null && !is_numeric($yearQuery)) {
            abort(404);
        }
        if ($periodRequest !== null && !in_array($periodRequest, ['1', '2'])) {
            abort(404);
        }

        $user = auth()->user();

        $status = self::ADMIN_STATUS;

        $periods = [];

        $period = '';
        $year = '';

        $badge = [];
        $data = [];

        $periodId = '';

        $doneCount = 0;
        $allCount = 0;

        $statusIndex = 0;
        if ($statusRequest === 'undone') {
            $statusIndex = 1;
        } else if ($statusRequest === 'done') {
            $statusIndex = 2;
        }
        $status[$statusIndex] = [
            ...$status[$statusIndex],
            'selected' => true,
        ];

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = $currentDate->format('Y');

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
            $year = $yearQuery ?? end($years);
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

            $period = $periodRequest ?? end($periods)['value'];

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
                ->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance, $user): void {
                    $query->where('status', 'aktif');
                    if ($statusIndex === 1) {
                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance, $user): void {
                            $query->whereBelongsTo($user->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    } else if ($statusIndex === 2) {
                        $query->whereHas('realization', function (Builder $query) use ($periodInstance, $user): void {
                            $query->whereBelongsTo($user->unit)
                                ->whereBelongsTo($periodInstance, 'period');
                        });
                    }
                })
                ->with([
                    'kegiatan' => function (HasMany $query) use ($statusIndex, $periodInstance, $user): void {
                        $query->whereHas('indikatorKinerja', function (Builder $query) use ($statusIndex, $periodInstance, $user): void {
                            $query->where('status', 'aktif');
                            if ($statusIndex === 1) {
                                $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance, $user): void {
                                    $query->whereBelongsTo($user->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                });
                            } else if ($statusIndex === 2) {
                                $query->whereHas('realization', function (Builder $query) use ($periodInstance, $user): void {
                                    $query->whereBelongsTo($user->unit)
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
                                'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance, $user): void {
                                    $query->where('status', 'aktif');
                                    if ($statusIndex === 1) {
                                        $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance, $user): void {
                                            $query->whereBelongsTo($user->unit)
                                                ->whereBelongsTo($periodInstance, 'period');
                                        });
                                    } else if ($statusIndex === 2) {
                                        $query->whereHas('realization', function (Builder $query) use ($periodInstance, $user): void {
                                            $query->whereBelongsTo($user->unit)
                                                ->whereBelongsTo($periodInstance, 'period');
                                        });
                                    }
                                }
                            ]);
                    },
                    'kegiatan.indikatorKinerja' => function (HasMany $query) use ($statusIndex, $periodInstance, $user): void {
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance, $user): void {
                                $query->whereBelongsTo($user->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance, $user): void {
                                $query->whereBelongsTo($user->unit)
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
                                'realization AS yearRealization' => function (Builder $query) use ($user): void {
                                    $query->whereBelongsTo($user->unit)
                                        ->whereDoesntHave('period');
                                }
                            ], 'realization')
                            ->withAggregate([
                                'realization AS realization' => function (Builder $query) use ($periodInstance, $user): void {
                                    $query->whereBelongsTo($user->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'realization')
                            ->withAggregate([
                                'realization AS link' => function (Builder $query) use ($periodInstance, $user): void {
                                    $query->whereBelongsTo($user->unit)
                                        ->whereBelongsTo($periodInstance, 'period');
                                }
                            ], 'link')
                            ->withAggregate([
                                'target AS target' => function (Builder $query) use ($user): void {
                                    $query->whereBelongsTo($user->unit);
                                }
                            ], 'target');
                    },
                    'kegiatan.indikatorKinerja.textSelections',
                ])
                ->orderBy('number')
                ->select([
                    'name AS ss',
                    'number',
                    'id',
                ])
                ->withCount([
                    'indikatorKinerja AS rowspan' => function (Builder $query) use ($statusIndex, $periodInstance, $user): void {
                        $query->where('status', 'aktif');
                        if ($statusIndex === 1) {
                            $query->whereDoesntHave('realization', function (Builder $query) use ($periodInstance, $user): void {
                                $query->whereBelongsTo($user->unit)
                                    ->whereBelongsTo($periodInstance, 'period');
                            });
                        } else if ($statusIndex === 2) {
                            $query->whereHas('realization', function (Builder $query) use ($periodInstance, $user): void {
                                $query->whereBelongsTo($user->unit)
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
                    'indikatorKinerja AS done' => function (Builder $query) use ($periodInstance, $user): void {
                        $query->where('status', 'aktif')
                            ->whereHas('realization', function (Builder $query) use ($periodInstance, $user): void {
                                $query->whereBelongsTo($user->unit)
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
            'statusRequest',
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
            'user',
        ]));
    }
}
