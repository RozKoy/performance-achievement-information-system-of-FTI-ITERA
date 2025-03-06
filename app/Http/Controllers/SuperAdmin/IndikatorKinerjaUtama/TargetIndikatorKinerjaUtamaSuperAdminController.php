<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use App\Http\Requests\IndikatorKinerjaUtama\AddTargetRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\IKUYear;
use App\Models\Unit;

class TargetIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @param string $year
     * @return Factory|View
     */
    public function view(string $year): Factory|View
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $user = auth()->user();

        $yearInstance = IKUYear::where('year', $year)
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
                        ->select([
                            'name AS ikk',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerjaProgram', function (Builder $query): void {
                        $query->where('status', 'aktif');
                    })
                        ->select([
                            'name AS ps',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number')
                        ->withCount([
                            'indikatorKinerjaProgram AS rowspan' => function (Builder $query): void {
                                $query->where('status', 'aktif');
                            }
                        ]);
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query): void {
                    $query->where('status', 'aktif')
                        ->select([
                            'name AS ikp',
                            'definition',
                            'type',
                            'id',

                            'program_strategis_id',
                        ])
                        ->orderBy('number')
                        ->with('target', function (HasMany $query): void {
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
            ->map(function ($item): array {
                $temp = $item->indikatorKinerjaKegiatan->map(function ($item): array {
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

        $units = Unit::where(function (Builder $query) use ($year): void {
            $query->whereNotNull('deleted_at')
                ->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year): void {
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

        return view('super-admin.achievement.iku.target', compact([
            'units',
            'data',
            'user',
            'year',
        ]));
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\AddTargetRequest $request
     * @param string $year
     * @return RedirectResponse
     */
    public function action(AddTargetRequest $request, string $year): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $targets = $request['target'];

        $indikatorKinerjaProgram = IndikatorKinerjaProgram::where('status', 'aktif')
            ->whereHas('programStrategis', function (Builder $query) use ($year): void {
                $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($year): void {
                    $query->whereHas('sasaranKegiatan', function (Builder $query) use ($year): void {
                        $query->whereHas('time', function (Builder $query) use ($year): void {
                            $query->where('year', $year);
                        });
                    });
                });
            })
            ->get();

        $units = Unit::where(function (Builder $query) use ($year): void {
            $query->whereNotNull('deleted_at')
                ->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year): void {
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

        foreach ($indikatorKinerjaProgram as $ikp) {
            foreach ($units as $unit) {
                try {
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
                    } else {
                        $ikp->target()->where('unit_id', $unit->id)->forceDelete();
                    }
                } catch (\Exception $e) {
                }
            }
            $newTarget = $ikp->target()->sum('target');
            if ($ikp->mode === IndikatorKinerjaProgram::MODE_SINGLE) {
                $newTarget = $ikp->target()->average('target');
                if (!ctype_digit(text: (string) $newTarget)) {
                    $newTarget = number_format((float) $newTarget, 2);
                }
            }

            if ($newTarget) {
                $realization = $ikp->achievements()->count();
                if ($ikp->mode === IndikatorKinerjaProgram::MODE_SINGLE) {
                    $realization = $ikp->singleAchievements()->average('value') ?? $newTarget - 1;
                    if (!ctype_digit(text: (string) $realization)) {
                        $realization = number_format((float) $realization, 2);
                    }
                }

                $evaluation = $ikp->evaluation()->firstOrNew();

                $evaluation->target = $newTarget;
                $evaluation->status = $realization >= $newTarget;

                $evaluation->save();
            } else {
                $ikp->evaluation()->forceDelete();
            }
        }

        return _ControllerHelpers::Back()->with('success', "Berhasil memperbaharui target indikator kinerja utama tahun $year");
    }
}
