<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use App\Http\Requests\RencanaStrategis\AddTargetRequest;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\IndikatorKinerja;
use App\Models\RSYear;
use App\Models\Unit;

class TargetRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @param string $year
     * @return Factory|View
     */
    public function view(string $year): Factory|View
    {
        HomeRencanaStrategisSuperAdminController::CheckRoutine();

        $user = auth()->user();

        $yearInstance = RSYear::where('year', $year)->firstOrFail();

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('indikatorKinerja', function (Builder $query): void {
                $query->where('status', 'aktif');
            })
            ->with([
                'kegiatan' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerja', function (Builder $query): void {
                        $query->where('status', 'aktif');
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
                                $query->where('status', 'aktif');
                            }
                        ]);
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query): void {
                    $query->where('status', 'aktif')
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
                'indikatorKinerja AS rowspan' => function (Builder $query): void {
                    $query->where('status', 'aktif');
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
            'user',
            'year',
        ]));
    }

    /**
     * @param \App\Http\Requests\RencanaStrategis\AddTargetRequest $request
     * @param string $year
     * @return RedirectResponse
     */
    public function action(AddTargetRequest $request, string $year): RedirectResponse
    {
        $targets = $request['target'];

        $indikatorKinerja = IndikatorKinerja::where('status', 'aktif')
            ->whereHas('kegiatan', function (Builder $query) use ($year): void {
                $query->whereHas('sasaranStrategis', function (Builder $query) use ($year): void {
                    $query->whereHas('time', function (Builder $query) use ($year): void {
                        $query->where('year', $year);
                    });
                });
            })
            ->get();

        $units = Unit::withTrashed()
            ->where(function (Builder $query) use ($year): void {
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
            ->get();

        foreach ($indikatorKinerja as $ik) {
            foreach ($units as $unit) {
                try {
                    if ($targets["$ik->id-$unit->id"] !== null) {
                        if (!is_numeric($targets["$ik->id-$unit->id"]) && ($ik->type === IndikatorKinerja::TYPE_PERCENT || $ik->type === IndikatorKinerja::TYPE_NUMBER)) {
                            return _ControllerHelpers::BackWithInputWithErrors(["target.$ik->id-$unit->id" => 'Realisasi tidak sesuai dengan tipe data']);
                        }

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
                } catch (\Exception $e) {
                }
            }

            if ($ik->type !== IndikatorKinerja::TYPE_TEXT) {
                $newTarget = $ik->target()->sum('target');
                if ($ik->type === IndikatorKinerja::TYPE_PERCENT) {
                    $newTarget = $ik->target()->average('target');
                    if (!ctype_digit(text: (string) $newTarget)) {
                        $newTarget = number_format((float) $newTarget, 2);
                    }
                }

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
            } else {
                try {
                    if ($targets[(string) $ik->id] !== null) {
                        $evaluation = $ik->evaluation()->firstOrNew();

                        $evaluation->target = $targets[(string) $ik->id];
                        $evaluation->status ??= false;

                        $evaluation->save();
                    } else {
                        $ik->evaluation()->forceDelete();
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return _ControllerHelpers::Back()->with('success', "Berhasil memperbaharui target tahun $year");
    }
}
