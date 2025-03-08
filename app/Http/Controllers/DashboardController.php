<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use App\Models\IKUYear;
use App\Models\Unit;

class DashboardController extends Controller
{
    /**
     * Super admin iku dashboard function
     * @param string $year
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function iku(string $year): Factory|View
    {
        $yearInstance = IKUYear::withTrashed()->where('year', $year)->firstOrFail();

        $datasets = collect();
        $idLists = collect();
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
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->select([
                        'name AS ikp',
                        'mode',
                        'id',

                        'program_strategis_id',
                    ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.singleAchievements',
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.achievements',
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.target',
            ])
            ->select([
                'name AS sk',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $units = Unit::withTrashed()
            ->where(function (Builder $query) use ($year) {
                $query->whereNotNull('deleted_at')->where(function (Builder $query) use ($year) {
                    $query->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year) {
                        $query->whereHas('period', function (Builder $query) use ($year) {
                            $query->whereHas('year', function (Builder $query) use ($year) {
                                $query->where('year', $year);
                            });
                        });
                    })
                        ->orWhereHas('indikatorKinerjaUtamaTarget', function (Builder $query) use ($year) {
                            $query->whereHas('indikatorKinerjaProgram', function (Builder $query) use ($year) {
                                $query->whereHas('programStrategis', function (Builder $query) use ($year) {
                                    $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($year) {
                                        $query->whereHas('sasaranKegiatan', function (Builder $query) use ($year) {
                                            $query->whereHas('time', function (Builder $query) use ($year) {
                                                $query->where('year', $year);
                                            });
                                        });
                                    });
                                });
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
            ->latest()
            ->get();


        $data->each(function ($item) use ($idLists, $datasets, $units) {
            $item->indikatorKinerjaKegiatan->each(function ($item) use ($idLists, $datasets, $units) {
                $item->programStrategis->each(function ($item) use ($idLists, $datasets, $units) {
                    $item->indikatorKinerjaProgram->each(function ($item) use ($idLists, $datasets, $units) {
                        $realizationTemp = collect();
                        $targetTemp = collect();
                        $unitTemp = collect();
                        $units->each(function ($unit) use ($realizationTemp, $targetTemp, $unitTemp, $item) {
                            if ($item->mode === 'table') {
                                $realizationTemp->push($item->achievements->where('unit_id', $unit->id)->count());
                            } else {
                                $realizationTemp->push($item->singleAchievements->where('unit_id', $unit->id)->average('value'));
                            }
                            $targetTemp->push($item->target->firstWhere('unit_id', $unit->id)?->target ?? 0);
                            $unitTemp->push($unit->short_name);
                        });
                        $datasets->put($item->id, [
                            'realization' => $realizationTemp->toArray(),
                            'target' => $targetTemp->toArray(),
                            'unit' => $unitTemp->toArray(),
                        ]);
                        $idLists->push($item->id);
                    });
                });
            });
        });

        $ikuYearList = IKUYear::orderBy('year')
            ->pluck('year')
            ->map(function ($item) use ($year) {
                if ($item === $year) {
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

        $previousRoute = route('super-admin-dashboard', ['ikuYear' => $year]);

        return view('super-admin.dashboard.iku', compact([
            'previousRoute',
            'ikuYearList',
            'datasets',
            'idLists',
            'data',
        ]));
    }
}
