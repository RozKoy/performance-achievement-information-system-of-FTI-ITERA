<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\IKUPeriod;
use App\Models\IKUYear;

class HomeIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @return void
     */
    public static function CheckRoutine(): void
    {
        IKUPeriod::whereDate('deadline', '<', Carbon::now())
            ->update([
                'deadline' => null,
                'status' => false,
            ]);
    }

    /**
     * @param string $yearId
     * @param string $value
     * @return void
     */
    public static function PeriodFirstOrNew(string $yearId, string $value): void
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
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        self::CheckRoutine();

        $yearQuery = $request->query('year');

        if (isset($yearQuery) && !is_numeric($yearQuery)) {
            abort(404);
        }

        $user = auth()->user();

        $currentYearInstance = IKUYear::currentTime();

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentYear = $currentDate->format('Y');
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

        $year = $yearQuery ?? end($years);
        $yearInstance = IKUYear::where('year', $year)->firstOrFail();

        if (
            ($year !== $currentYear && $yearInstance->periods->count() !== 4)
            ||
            ($year === $currentYear && $yearInstance->periods->count() < (int) $currentPeriod)
        ) {
            foreach (['1', '2', '3', '4'] as $key => $value) {
                if ($year !== $currentYear || (int) $value <= (int) $currentPeriod) {
                    self::PeriodFirstOrNew($yearInstance->id, $value);
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
            ->map(function ($item): array {
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
                'indikatorKinerjaKegiatan' => function (HasMany $query): void {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram')
                        ->select([
                            'name AS ikk',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerjaProgram')
                        ->select([
                            'name AS ps',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number')
                        ->withCount('indikatorKinerjaProgram AS rowspan');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query): void {
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
                            'achievements AS tw1' => function (Builder $query): void {
                                $query->where('status', true)
                                    ->whereHas('period', function (Builder $query): void {
                                        $query->where('period', '1');
                                    });
                            },
                            'achievements AS tw2' => function (Builder $query): void {
                                $query->where('status', true)
                                    ->whereHas('period', function (Builder $query): void {
                                        $query->where('period', '2');
                                    });
                            },
                            'achievements AS tw3' => function (Builder $query): void {
                                $query->where('status', true)
                                    ->whereHas('period', function (Builder $query): void {
                                        $query->where('period', '3');
                                    });
                            },
                            'achievements AS tw4' => function (Builder $query): void {
                                $query->where('status', true)
                                    ->whereHas('period', function (Builder $query): void {
                                        $query->where('period', '4');
                                    });
                            },
                            'achievements AS all' => function (Builder $query): void {
                                $query->where('status', true);
                            },
                        ])
                        ->withAvg([
                            'singleAchievements AS tw1Single' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '1');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS tw2Single' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '2');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS tw3Single' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '3');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS tw4Single' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
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
            ->map(function ($item): array {
                $temp = $item->indikatorKinerjaKegiatan->map(function ($item): array {
                    return [
                        ...$item->toArray(),

                        'rowspan' => $item->programStrategis->sum('rowspan'),
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
            'user',
        ]));
    }
}
