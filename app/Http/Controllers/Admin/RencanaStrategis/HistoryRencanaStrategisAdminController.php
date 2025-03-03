<?php

namespace App\Http\Controllers\Admin\RencanaStrategis;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSPeriod;
use App\Models\RSYear;

class HistoryRencanaStrategisAdminController extends Controller
{
    protected $adminStatus = HomeRencanaStrategisAdminController::ADMIN_STATUS;

    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
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

        $status = $this->adminStatus;

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

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = $currentDate->format('Y');

        $years = RSPeriod::where('status', false)
            ->withAggregate('year AS year', 'year')
            ->orderBy('year')
            ->pluck('year')
            ->flatten()
            ->unique()
            ->toArray();

        if (count($years)) {
            $year = $yearQuery ?? end($years);
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

            $period = $periodRequest ?? end($periods)['value'];

            $periodInstance = RSPeriod::where('status', false)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
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
                                    $query->whereBelongsTo(related: $user->unit)
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
                    'kegiatan.indikatorKinerja.textSelections'
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

            $badge = [
                $period === '1' ? 'Januari - Juni' : 'Juli - Desember',
                $year
            ];
        }

        return view('admin.history.rs.home', compact([
            'statusRequest',
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
