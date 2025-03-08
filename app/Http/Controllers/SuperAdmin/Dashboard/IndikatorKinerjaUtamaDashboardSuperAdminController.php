<?php

namespace App\Http\Controllers\SuperAdmin\Dashboard;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Exports\MultipleSheets;
use App\Exports\IKUExport;
use App\Models\IKUYear;
use App\Models\Unit;

class IndikatorKinerjaUtamaDashboardSuperAdminController extends Controller
{
    /**
     * @param string $year
     * @return BinaryFileResponse
     */
    public function excelExport(string $year): BinaryFileResponse
    {
        $yearInstance = IKUYear::withTrashed()->where('year', $year)->firstOrFail();

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
                            'name',
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
                            'name',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query): void {
                    $query->with([
                        'singleAchievements',
                        'achievements',
                        'evaluation',
                        'target',
                    ])
                        ->where('status', 'aktif')
                        ->select([
                            'definition',
                            'mode',
                            'name',
                            'type',
                            'id',

                            'program_strategis_id',
                        ])
                        ->orderBy('number');
                },
            ])
            ->select([
                'number',
                'name',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $units = Unit::withTrashed()
            ->where(function (Builder $query) use ($year): void {
                $query->whereNotNull('deleted_at')->where(function (Builder $query) use ($year): void {
                    $query->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year): void {
                        $query->whereHas('period', function (Builder $query) use ($year): void {
                            $query->whereHas('year', function (Builder $query) use ($year): void {
                                $query->where('year', $year);
                            });
                        });
                    })
                        ->orWhereHas('indikatorKinerjaUtamaTarget', function (Builder $query) use ($year): void {
                            $query->whereHas('indikatorKinerjaProgram', function (Builder $query) use ($year): void {
                                $query->whereHas('programStrategis', function (Builder $query) use ($year): void {
                                    $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($year): void {
                                        $query->whereHas('sasaranKegiatan', function (Builder $query) use ($year): void {
                                            $query->whereHas('time', function (Builder $query) use ($year): void {
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

        $temp1 = collect();
        $temp2 = collect();
        foreach ($units as $unit) {
            $temp1->add("$unit->name ($unit->short_name)");
            $temp1->add('');
            $temp2->add("target $year");
            $temp2->add('realisasi');
        }

        $names = [
            "Tahun $year",
            "TW 1",
            "TW 2",
            "TW 3",
            "TW 4",
        ];

        $headers1 = [
            'no',
            'sasaran kegiatan',
            'indikator kinerja kegiatan',
            'program strategis',
            'indikator kinerja program',
            'tipe',
            'definisi operasional',
            ...$temp1->toArray(),
        ];
        $headers2 = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            ...$temp2->toArray(),
        ];

        $collection = collect([
            collect([
                $headers1,
                $headers2
            ]),
            collect([
                $headers1,
                $headers2
            ]),
            collect([
                $headers1,
                $headers2
            ]),
            collect([
                $headers1,
                $headers2
            ]),
            collect([
                $headers1,
                $headers2
            ]),
        ]);

        foreach ($collection as $itemKey => $item) {
            $period = [];
            if ($itemKey === 0) {
                $period = [1, 2, 3, 4];
            } else if ($itemKey === 1) {
                $period = [1];
            } else if ($itemKey === 2) {
                $period = [2];
            } else if ($itemKey === 3) {
                $period = [3];
            } else if ($itemKey === 4) {
                $period = [4];
            }

            foreach ($data as $skKey => $sk) {
                foreach ($sk->indikatorKinerjaKegiatan as $ikkKey => $ikk) {
                    foreach ($ikk->programStrategis as $psKey => $ps) {
                        foreach ($ps->indikatorKinerjaProgram as $ikpKey => $ikp) {
                            $temp = ['', '', '', '', '', '', ''];

                            if (!$ikpKey) {
                                if (!$psKey) {
                                    if (!$ikkKey) {
                                        $temp[0] = $sk->number;
                                        $temp[1] = $sk->name;
                                    }
                                    $temp[2] = $ikk->name;
                                }
                                $temp[3] = $ps->name;
                            }

                            $temp[4] = $ikp->name;
                            $temp[5] = $ikp->type;
                            $temp[6] = $ikp->definition;

                            foreach ($units as $unit) {
                                $temp[] = $ikp->target->firstWhere('unit_id', $unit->id)?->target ?? '';
                                if ($ikp->mode === 'table') {
                                    $temp[] = $ikp->achievements()
                                        ->where('status', true)
                                        ->where('unit_id', $unit->id)
                                        ->whereHas('period', function ($query) use ($period): void {
                                            $query->whereIn('period', $period);
                                        })
                                        ->count();
                                } else {
                                    $temp[] = $ikp->singleAchievements()
                                        ->where('unit_id', $unit->id)
                                        ->whereHas('period', function ($query) use ($period): void {
                                            $query->whereIn('period', $period);
                                        })
                                        ->average('value');
                                }
                            }

                            $item->add($temp);
                        }
                    }
                }
            }
        }

        return Excel::download(
            new MultipleSheets(
                IKUExport::class,
                $names,
                $collection->toArray()
            ),
            "indikator-kinerja-utama-tahun-$year.xlsx"
        );
    }
}
