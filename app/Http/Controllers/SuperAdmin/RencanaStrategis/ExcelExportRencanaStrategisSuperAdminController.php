<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Exports\RSExport;
use App\Models\RSPeriod;
use App\Models\RSYear;

class ExcelExportRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return BinaryFileResponse
     */
    public function action(Request $request): BinaryFileResponse
    {
        $periodQuery = $request->query('period');
        $yearQuery = $request->query('year');

        if (isset($yearQuery) && !is_numeric($yearQuery)) {
            abort(404);
        }
        if (isset($periodQuery) && !in_array($periodQuery, ['1', '2', '3'])) {
            abort(404);
        }

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentPeriod = $currentMonth <= 6 ? '1' : '2';
        $currentYear = $currentDate->format('Y');

        $years = RSYear::orderBy('year')
            ->pluck('year')
            ->toArray();

        $year = $yearQuery ?? end($years);
        $yearInstance = RSYear::where('year', $year)->firstOrFail();

        HomeRencanaStrategisSuperAdminController::PeriodFirstOrNew($yearInstance->id, '1');
        if ($year !== $currentYear || $currentPeriod === '2') {
            HomeRencanaStrategisSuperAdminController::PeriodFirstOrNew($yearInstance->id, '2');
        }

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->toArray();

        if (count($periods) === 2) {
            $periods[] = '3';
        }

        $period = $periodQuery ?? end($periods);

        if ((int) $period > count($periods)) {
            abort(404);
        }

        $periodInstance = null;
        if ($period !== '3') {
            $periodInstance = RSPeriod::where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->firstOrFail();
        }

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('indikatorKinerja')
            ->with([
                'kegiatan' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerja')
                        ->orderBy('number')
                        ->select([
                            'name',
                            'id',

                            'sasaran_strategis_id',
                        ]);
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query) use ($periodInstance): void {
                    $query->orderBy('number')
                        ->select([
                            'name',
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
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS status', 'status');
                },
                'kegiatan.indikatorKinerja.textSelections',
            ])
            ->orderBy('number')
            ->select([
                'number',
                'name',
                'id',
            ])
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
            ])
            ->get();

        $success = $data->sum('success');
        $failed = $data->sum('failed');

        $collection = collect([
            ['Tahun', $year],
            ['Periode', $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni')],
        ]);

        if ($period === '3') {
            $collection->add(['Tercapai', $success, 'Tidak Tercapai', $failed]);
            $collection->add([
                'no',
                'sasaran strategis',
                'kegiatan',
                'indikator kinerja',
                'realisasi',
                "target $year",
                "evaluasi",
                "tindak lanjut",
                "status",
            ]);
        } else {
            $collection->add([
                'no',
                'sasaran strategis',
                'kegiatan',
                'indikator kinerja',
                'realisasi',
            ]);
        }

        $data->each(function ($ss) use ($collection, $period): void {
            $ss->kegiatan->each(function ($k, $kIndex) use ($collection, $period, $ss): void {
                $k->indikatorKinerja->each(function ($ik, $ikIndex) use ($collection, $kIndex, $period, $ss, $k): void {
                    $temp = ['', '', '', '', '', '', '', '', ''];
                    if (!$ikIndex) {
                        if (!$kIndex) {
                            $temp[0] = $ss->number;
                            $temp[1] = $ss->name;
                        }
                        $temp[2] = $k->name;
                    }
                    $temp[3] = $ik->name;
                    $temp[4] = $ik->realization;
                    if ($ik->type === IndikatorKinerja::TYPE_TEXT) {
                        $temp[4] = $ik->textSelections()->firstWhere('id', $ik->realization)['value'] ?? '';
                    } else if ($ik->type === IndikatorKinerja::TYPE_PERCENT) {
                        $temp[4] = "$ik->realization%";
                    }

                    if ($period === '3') {
                        $temp[5] = $ik->target;
                        if ($ik->type === IndikatorKinerja::TYPE_TEXT) {
                            $temp[5] = $ik->textSelections()->firstWhere('id', $ik->target)['value'] ?? '';
                        } else if ($ik->type === IndikatorKinerja::TYPE_PERCENT) {
                            $temp[5] = "$ik->target%";
                        }
                        $temp[6] = $ik->evaluation;
                        $temp[7] = $ik->follow_up;
                        $temp[8] = $ik->status ? 'Tercapai' : 'Tidak tercapai';
                    }

                    $collection->add($temp);
                });
            });
        });

        return Excel::download(
            new RSExport($collection->toArray()),
            "rencana-strategis-$yearQuery.xlsx"
        );
    }
}
