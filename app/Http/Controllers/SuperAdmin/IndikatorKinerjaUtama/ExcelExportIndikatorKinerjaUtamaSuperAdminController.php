<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaProgram;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use App\Models\IKUSingleAchievement;
use Illuminate\Support\Carbon;
use App\Models\IKUAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Exports\IKUExport;
use App\Models\IKUYear;

class ExcelExportIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return BinaryFileResponse
     */
    public function action(Request $request): BinaryFileResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $yearQuery = $request->query('year');

        if (isset($yearQuery) && !is_numeric($yearQuery)) {
            abort(404);
        }

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
                    HomeIndikatorKinerjaUtamaSuperAdminController::PeriodFirstOrNew($yearInstance->id, $value);
                }
            }
        }

        $data = $yearInstance->sasaranKegiatan()
            ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram')
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query): void {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram')
                        ->select([
                            'name',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query): void {
                    $query->whereHas('indikatorKinerjaProgram')
                        ->select([
                            'name',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query): void {
                    $query->select([
                        'definition',
                        'mode',
                        'name',
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
                            'singleAchievements AS singleTw1' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '1');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS singleTw2' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '2');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS singleTw3' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '3');
                                });
                            }
                        ], 'value')
                        ->withAvg([
                            'singleAchievements AS singleTw4' => function (Builder $query): void {
                                $query->whereHas('period', function (Builder $query): void {
                                    $query->where('period', '4');
                                });
                            }
                        ], 'value')
                        ->withAvg('singleAchievements AS allSingle', 'value')
                        ->withAggregate('evaluation AS evaluation', 'evaluation')
                        ->withAggregate('evaluation AS follow_up', 'follow_up')
                        ->withAggregate('evaluation AS target', 'target')
                        ->withAggregate('evaluation AS status', 'status');
                },
            ])
            ->select([
                'number',
                'name',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $collection = collect([
            ['tahun', $year],
            [
                'no',
                'sasaran kegiatan',
                'indikator kinerja kegiatan',
                'program strategis',
                'indikator kinerja program',
                'tipe',
                'definisi operasional',
                "target $year",
                "realisasi $year",
                'tw1',
                'tw2',
                'tw3',
                'tw4',
                'kendala',
                'tindak lanjut',
                'status',
            ]
        ]);

        $data->each(function ($sk) use ($collection): void {
            $sk->indikatorKinerjaKegiatan->each(function ($ikk, $ikkIndex) use ($collection, $sk): void {
                $ikk->programStrategis->each(function ($ps, $psIndex) use ($collection, $ikkIndex, $ikk, $sk): void {
                    $ps->indikatorKinerjaProgram->each(function ($ikp, $ikpIndex) use ($collection, $ikkIndex, $psIndex, $ikk, $ps, $sk): void {
                        $temp = ['', '', '', '', '', '', '', '', '', '', '', '', '', '', '', ''];

                        if (!$ikpIndex) {
                            if (!$psIndex) {
                                if (!$ikkIndex) {
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
                        $temp[7] = $ikp->target;

                        if ($ikp->mode === IndikatorKinerjaProgram::MODE_TABLE) {
                            $temp[8] = $ikp->all;
                            $temp[9] = $ikp->tw1;
                            $temp[10] = $ikp->tw2;
                            $temp[11] = $ikp->tw3;
                            $temp[12] = $ikp->tw4;
                        } else {
                            $temp[8] = $ikp->allSingle;
                            if (!ctype_digit(text: (string) $temp[8])) {
                                $temp[8] = number_format((float) $temp[8], 2);
                            }
                            $temp[9] = $ikp->singleTw1;
                            if (!ctype_digit(text: (string) $temp[9])) {
                                $temp[9] = number_format((float) $temp[9], 2);
                            }
                            $temp[10] = $ikp->singleTw2;
                            if (!ctype_digit(text: (string) $temp[10])) {
                                $temp[10] = number_format((float) $temp[10], 2);
                            }
                            $temp[11] = $ikp->singleTw3;
                            if (!ctype_digit(text: (string) $temp[11])) {
                                $temp[11] = number_format((float) $temp[11], 2);
                            }
                            $temp[12] = $ikp->singleTw4;
                            if (!ctype_digit(text: (string) $temp[12])) {
                                $temp[12] = number_format((float) $temp[12], 2);
                            }
                        }

                        $temp[13] = $ikp->evaluation;
                        $temp[14] = $ikp->follow_up;
                        $temp[15] = $ikp->status ? 'Tercapai' : 'Tidak tercapai';

                        $collection->add($temp);
                    });
                });
            });
        });

        return Excel::download(
            new IKUExport($collection->toArray()),
            "indikator-kinerja-utama-$yearQuery.xlsx"
        );
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function detailAction(Request $request, IndikatorKinerjaProgram $ikp): BinaryFileResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $periodQuery = $request->query('period');

        if (isset($periodQuery) && !in_array($periodQuery, ['1', '2', '3', '4', '5'])) {
            abort(404);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $yearInstance = $sk->time;
        $year = $yearInstance->year;

        $periods = $yearInstance->periods()
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item) {
                $title = 'TW 1 | Jan - Mar';
                if ($item === '2') {
                    $title = 'TW 2 | Apr - Jun';
                } else if ($item === '3') {
                    $title = 'TW 3 | Jul - Sep';
                } else if ($item === '4') {
                    $title = 'TW 4 | Okt - Des';
                }

                return [
                    'title' => $title,
                    'value' => $item
                ];
            });

        if ($periods->count() === 4) {
            $periods->push([
                'title' => 'Januari - Desember',
                'value' => '5'
            ]);
        }

        $period = $periodQuery ?? $periods->last()['value'];

        if ((int) $period > $periods->count()) {
            abort(404);
        }

        $periodInstance = $yearInstance->periods()
            ->where('period', $period)
            ->first();

        $columns = $ikp->columns()
            ->select([
                'file',
                'name',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $data = collect([]);
        if ($ikp->mode === IndikatorKinerjaProgram::MODE_TABLE) {
            $data = IKUAchievement::withTrashed()
                ->with([
                    'data' => function (HasMany $query): void {
                        $query->select([
                            'achievement_id',
                            'column_id',
                            'data',
                        ])
                            ->withAggregate('column AS file', 'file');
                    }
                ])
                ->where(function (Builder $query) use ($periodInstance): void {
                    if ($periodInstance) {
                        $query->whereBelongsTo($periodInstance, 'period');
                    }
                })
                ->where('status', true)
                ->whereBelongsTo($ikp)
                ->select('id')
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();
        } else {
            $data = IKUSingleAchievement::withTrashed()
                ->where(function (Builder $query) use ($periodInstance): void {
                    if ($periodInstance) {
                        $query->whereBelongsTo($periodInstance, 'period');
                    }
                })
                ->whereBelongsTo($ikp)
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();
        }

        $achievementCount = $data->count();
        if ($ikp->mode === IndikatorKinerjaProgram::MODE_SINGLE) {
            $achievementCount = $data->average('value');
            if (!ctype_digit(text: (string) $achievementCount)) {
                $achievementCount = number_format((float) $achievementCount, 2);
            }
        }

        $data = $data->groupBy('unit');

        $evaluation = $ikp->evaluation;

        $first = collect(['no']);
        if ($ikp->mode === IndikatorKinerjaProgram::MODE_TABLE) {
            foreach ($columns as $column) {
                $first->add($column->name);
            }
        } else {
            $first->add('program studi');
            $first->add('realisasi');
            $first->add('bukti');
        }

        $collection = collect([
            ['tahun', $year],
            ['periode', $periods->firstWhere('value', $period)['title']],
            ['no', $sk->number, 'sasaran kegiatan', $sk->name],
            ['no', $ikk->number, 'indikator kinerja kegiatan', $ikk->name],
            ['no', $ps->number, 'program strategis', $ps->name],
            ['no', $ikp->number, 'indikator kinerja program', $ikp->name],
            ['definisi operasional', $ikp->definition, 'tipe', $ikp->type],
            ['realisasi', $achievementCount],
            $evaluation && $period === '5' ? ['target', $evaluation->target, 'kendala', $evaluation->evaluation, 'tindak lanjut', $evaluation->follow_up] : [],
            ['data'],
            $first->toArray(),
        ]);

        if ($ikp->mode === IndikatorKinerjaProgram::MODE_TABLE) {
            $data->each(function ($item, $key) use ($collection, $columns): void {
                $collection->add([$key]);
                $item->each(function ($col, $index) use ($collection, $columns): void {
                    $temp = collect([(string) ($index + 1)]);

                    $columns->each(function ($column) use ($temp, $col): void {
                        $find = $col['data']->firstWhere('column_id', $column->id);

                        if ($find) {
                            if ($find->file) {
                                $temp->add(url(asset("storage/$find->data")));
                            } else {
                                $temp->add($find->data);
                            }
                        } else {
                            $temp->add('');
                        }
                    });

                    $collection->add($temp->toArray());
                });
            });
        } else {
            $index = 1;
            foreach ($data as $key => $item) {
                $collection->add([$index, $key, $item->average('value'), $item->count() === 1 ? $item->first()->link : '']);
                $index++;
            }
        }

        return Excel::download(
            new IKUExport($collection->toArray()),
            Str::replace(['/', '\\'], '-', (string) $ikp->name) . '.xlsx'
        );
    }
}
