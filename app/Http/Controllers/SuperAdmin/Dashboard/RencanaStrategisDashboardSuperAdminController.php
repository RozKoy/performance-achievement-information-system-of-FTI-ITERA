<?php

namespace App\Http\Controllers\SuperAdmin\Dashboard;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\IndikatorKinerja;
use App\Exports\MultipleSheets;
use Illuminate\Support\Str;
use App\Exports\RSExport;
use App\Models\RSYear;
use App\Models\Unit;

class RencanaStrategisDashboardSuperAdminController extends Controller
{
    /**
     * @param string $year
     * @return BinaryFileResponse
     */
    public function excelExport(string $year): BinaryFileResponse
    {
        $yearInstance = RSYear::withTrashed()->where('year', $year)->firstOrFail();

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('kegiatan.indikatorKinerja', function (Builder $query): void {
                $query->where('status', 'aktif');
            })
            ->with([
                'kegiatan' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerja', function (Builder $query): void {
                        $query->where('status', 'aktif');
                    })
                        ->select([
                            'name',
                            'id',

                            'sasaran_strategis_id',
                        ])
                        ->orderBy('number');
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query): void {
                    $query->with([
                        'realization',
                        'target',
                    ])
                        ->where('status', 'aktif')
                        ->select([
                            'number',
                            'name',
                            'type',
                            'id',

                            'kegiatan_id',
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
                    $query->whereHas('rencanaStrategis', function (Builder $query) use ($year): void {
                        $query->whereHas('period', function (Builder $query) use ($year): void {
                            $query->whereHas('year', function (Builder $query) use ($year): void {
                                $query->where('year', $year);
                            });
                        });
                    })
                        ->orWhereHas('rencanaStrategisTarget', function (Builder $query) use ($year): void {
                            $query->whereHas('indikatorKinerja', function (Builder $query) use ($year): void {
                                $query->whereHas('kegiatan', function (Builder $query) use ($year): void {
                                    $query->whereHas('sasaranStrategis', function (Builder $query) use ($year): void {
                                        $query->whereHas('time', function (Builder $query) use ($year): void {
                                            $query->where('year', $year);
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
            "Semester Ganjil",
            "Semester Genap",
        ];

        $headers1 = [
            'no',
            'sasaran strategis',
            'kegiatan',
            'indikator kinerja',
            ...$temp1->toArray(),
        ];
        $headers2 = [
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
        ]);

        foreach ($collection as $itemKey => $item) {
            $period = [];
            if ($itemKey === 0) {
                $period = [1, 2];
            } else if ($itemKey === 1) {
                $period = [1];
            } else if ($itemKey === 2) {
                $period = [2];
            }

            foreach ($data as $skKey => $ss) {
                foreach ($ss->kegiatan as $kKey => $k) {
                    foreach ($k->indikatorKinerja as $ikKey => $ik) {
                        $temp = ['', '', '', ''];

                        if (!$ikKey) {
                            if (!$kKey) {
                                $temp[0] = $ss->number;
                                $temp[1] = $ss->name;
                            }
                            $temp[2] = $k->name;
                        }
                        $temp[3] = $ik->name;

                        foreach ($units as $unit) {
                            $tempQuery = $ik->realization()
                                ->where('unit_id', $unit->id)
                                ->whereHas('period', function ($query) use ($period): void {
                                    $query->whereIn('period', $period);
                                });
                            if ($ik->type === IndikatorKinerja::TYPE_TEXT) {
                                $temp[] = $ik->textSelections->firstWhere('id', $ik->target->firstWhere('unit_id', $unit->id)?->target ?? '')?->value ?? '';
                                $tempArray = [];
                                foreach ($tempQuery->pluck('realization')->toArray() as $tempRealization) {
                                    $tempArray[] = Str::isUuid($tempRealization)
                                        ? collect($item['text_selections'])->firstWhere(
                                            'id',
                                            $tempRealization,
                                        )?->value ?? 'NONE'
                                        : $tempRealization;
                                }
                                $temp[] = join(',', $tempArray);
                            } else if ($ik->type === IndikatorKinerja::TYPE_NUMBER) {
                                $temp[] = $ik->target->firstWhere('unit_id', $unit->id)?->target ?? '';
                                $temp[] = $tempQuery->sum('realization');
                            } else if ($ik->type === IndikatorKinerja::TYPE_PERCENT) {
                                $temp[] = $ik->target->firstWhere('unit_id', $unit->id)?->target ?? '';
                                $temp[] = $tempQuery->average('realization');
                            }
                        }

                        $item->add($temp);
                    }
                }
            }
        }

        return Excel::download(
            new MultipleSheets(
                RSExport::class,
                $names,
                $collection->toArray()
            ),
            "rencana-strategis-tahun-$year.xlsx"
        );
    }
}
