<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\SasaranStrategis;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Kegiatan;
use App\Models\RSPeriod;
use App\Models\RSYear;
use App\Models\Unit;

class HomeRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @return void
     */
    public static function CheckRoutine(): void
    {
        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = $currentDate->format('Y');

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
     * @param string $yearId
     * @param string $value
     * @return void
     */
    public static function PeriodFirstOrNew(string $yearId, string $value): void
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
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        self::CheckRoutine();

        $periodQuery = $request->query('period');
        $yearQuery = $request->query('year');

        if (isset($yearQuery) && !is_numeric($yearQuery)) {
            abort(404);
        }
        if (isset($periodQuery) && !in_array($periodQuery, ['1', '2', '3'])) {
            abort(404);
        }

        $user = auth()->user();

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

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = $currentDate->format('Y');

        $years = RSYear::orderBy('year')
            ->pluck('year')
            ->toArray();

        $year = $yearQuery ?? end($years);
        $yearInstance = RSYear::where('year', $year)->firstOrFail();

        self::PeriodFirstOrNew($yearInstance->id, '1');
        if ($year !== $currentYear || $currentPeriod === '2') {
            self::PeriodFirstOrNew($yearInstance->id, '2');
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

        $period = $periodQuery ?? end($periods)['value'];

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
            'user',
        ]));
    }
}
