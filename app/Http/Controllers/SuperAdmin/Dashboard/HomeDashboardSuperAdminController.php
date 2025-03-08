<?php

namespace App\Http\Controllers\SuperAdmin\Dashboard;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\IKUYear;
use App\Models\RSYear;
use App\Models\Unit;

class HomeDashboardSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return Factory|View
     */
    public function view(Request $request): Factory|View
    {
        $currentDate = Carbon::now();

        $ikuYear = $request->query('ikuYear') ?? strval($currentDate->year);
        $rsYear = $request->query('rsYear') ?? strval($currentDate->year);

        $ikuYearList = IKUYear::orderBy('year')
            ->pluck('year')
            ->map(function ($item) use ($ikuYear): array {
                if ($item === $ikuYear) {
                    return [
                        'selected' => true,
                        'value' => $item,
                        'text' => $item,
                    ];
                }
                return [
                    'value' => $item,
                    'text' => $item,
                ];
            })
            ->toArray();

        $rsYearList = RSYear::orderBy('year')
            ->pluck('year')
            ->map(function ($item) use ($rsYear): array {
                if ($item === $rsYear) {
                    return [
                        'selected' => true,
                        'value' => $item,
                        'text' => $item,
                    ];
                }
                return [
                    'value' => $item,
                    'text' => $item,
                ];
            })
            ->toArray();

        $iku = IKUYear::where('year', $ikuYear)->with([
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query): void {
                $query->whereHas('indikatorKinerjaProgram')
                    ->withCount([
                        'indikatorKinerjaProgram AS success' => function (Builder $query): void {
                            $query->whereHas('evaluation', function (Builder $query): void {
                                $query->where('status', true);
                            });
                        },
                        'indikatorKinerjaProgram AS failed' => function (Builder $query): void {
                            $query->whereDoesntHave('evaluation')
                                ->orWhereHas('evaluation', function (Builder $query): void {
                                    $query->where('status', false);
                                });
                        },
                    ]);
            },
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query): void {
                $query->where('status', 'aktif');
            },
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.singleAchievements',
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.achievements',
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.unitStatus',
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.target',
        ])->first();

        $rs = RSYear::where('year', $rsYear)->with([
            'sasaranStrategis.kegiatan' => function (HasMany $query): void {
                $query->whereHas('indikatorKinerja')
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
                    ]);
            },
            'sasaranStrategis.kegiatan.indikatorKinerja' => function (HasMany $query): void {
                $query->where('status', 'aktif');
            },
            'sasaranStrategis.kegiatan.indikatorKinerja.textSelections',
            'sasaranStrategis.kegiatan.indikatorKinerja.realization',
            'sasaranStrategis.kegiatan.indikatorKinerja.target',
        ])->first();

        $units = Unit::latest()
            ->select([
                'short_name',
                'name',
                'id',
            ])
            ->get()
            ->toArray();

        $ikuIndikatorKinerjaProgram = collect();
        $iku = [
            'success' => $iku?->sasaranKegiatan?->sum(function ($item) use ($ikuIndikatorKinerjaProgram): int {
                return $item->indikatorKinerjaKegiatan->sum(function ($item) use ($ikuIndikatorKinerjaProgram): int {
                    $item->programStrategis->each(function ($item) use ($ikuIndikatorKinerjaProgram): void {
                        $ikuIndikatorKinerjaProgram->push(...$item->indikatorKinerjaProgram);
                    });
                    return $item->programStrategis->sum('success');
                });
            }),
            'failed' => $iku?->sasaranKegiatan?->sum(function ($item): int {
                return $item->indikatorKinerjaKegiatan->sum(function ($item): int {
                    return $item->programStrategis->sum('failed');
                });
            }),
        ];

        $rsIndikatorKinerja = collect();
        $rs = [
            'success' => $rs?->sasaranStrategis?->sum(function ($item) use ($rsIndikatorKinerja): int {
                $item->kegiatan->each(function ($item) use ($rsIndikatorKinerja): void {
                    $rsIndikatorKinerja->push(...$item->indikatorKinerja);
                });
                return $item->kegiatan->sum('success');
            }),
            'failed' => $rs?->sasaranStrategis?->sum(function ($item): int {
                return $item->kegiatan->sum('failed');
            }),
        ];

        $iku['sum'] = $iku['success'] + $iku['failed'];
        $rs['sum'] = $rs['success'] + $rs['failed'];

        $ikuPercent = $iku['sum'] ? $iku['success'] * 100 / $iku['sum'] : 0;
        $rsPercent = $rs['sum'] ? $rs['success'] * 100 / $rs['sum'] : 0;

        $ikuPercent = number_format((float) $ikuPercent, 2, '.', '');
        $rsPercent = number_format((float) $rsPercent, 2, '.', '');

        $rsIndikatorKinerja = $rsIndikatorKinerja->toArray();

        return view('super-admin.dashboard.home', compact([
            'ikuIndikatorKinerjaProgram',
            'rsIndikatorKinerja',
            'ikuYearList',
            'rsYearList',
            'ikuPercent',
            'rsPercent',
            'ikuYear',
            'rsYear',
            'units',
            'iku',
            'rs',
        ]));
    }
}
