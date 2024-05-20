<?php

namespace App\Http\Controllers;

use App\Models\RSAchievement;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Http\Requests\RencanaStrategis\AddRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerja;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\RSPeriod;
use App\Models\RSYear;

class RencanaStrategisController extends Controller
{
    /*
    | -----------------------------------------------------------------
    | SUPER ADMIN
    | -----------------------------------------------------------------
    */

    public function homeView(Request $request)
    {
        $currentMonth = (int) Carbon::now()->format('m');
        $currentYear = Carbon::now()->format('Y');

        $year = isset($request->year) ? $request->year : $currentYear;

        if ($year === $currentYear) {
            $year = RSYear::currentTime();
        } else {
            $year = RSYear::where('year', $year)->firstOrFail();
        }

        $periodList = ['1'];
        if ($year->year !== $currentYear || $currentMonth >= 6) {
            $periodList[] = '2';
        }

        foreach ($periodList as $key => $value) {
            $temp = RSPeriod::firstOrNew([
                'year_id' => $year->id,
                'period' => $value,
            ], [
                'status' => true,
            ]);

            if ($temp->id === null) {
                $temp->save();

                $temp->deadline_id = $temp->id;
                $temp->save();
            }
        }

        return view('super-admin.achievement.rs.home');
    }


    /*
    | -----------------------------------------------------------------
    | ADMIN
    | -----------------------------------------------------------------
    */

    public function homeViewAdmin(Request $request)
    {
        if (isset($request->year)) {
            if (!is_numeric($request->year)) {
                abort(404);
            }
        }
        if (isset($request->period)) {
            if ($request->period !== '1' && $request->period !== '2') {
                abort(404);
            }
        }

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth < 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $years = RSPeriod::where('status', true)
            ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->withAggregate('year AS year', 'year')
            ->orderBy('year')
            ->get()
            ->pluck('year')
            ->flatten()
            ->unique()
            ->toArray();

        if (count($years)) {
            $year = isset($request->year) ? $request->year : $years[count($years) - 1];
            $yearInstance = RSYear::where('year', $year)->firstOrFail();

            $temp = $yearInstance->periods()
                ->where('status', true)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->orderBy('period')
                ->pluck('period')
                ->flatten()
                ->unique()
                ->toArray();

            if (!count($temp)) {
                abort(404);
            }

            $periods = array_map(function ($item) {
                $title = 'Januari - Juni';
                if ($item === '2') {
                    $title = 'Juli - Desember';
                }
                return [
                    'title' => $title,
                    'value' => $item
                ];
            }, $temp);
            $period = isset($request->period) ? $request->period : $periods[count($periods) - 1]['value'];
            $periodInstance = RSPeriod::where('status', true)
                ->where('year_id', $yearInstance->id)
                ->where('period', $period)
                ->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                    $query->where('period', $currentPeriod)
                        ->whereHas('year', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                })
                ->firstOrFail();

            $data = $yearInstance->sasaranStrategis()
                ->whereHas('kegiatan.indikatorKinerja', function (Builder $query) {
                    $query->where('status', 'aktif');
                })
                ->with([
                    'kegiatan' => function (HasMany $query) {
                        $query->whereHas('indikatorKinerja', function (Builder $query) {
                            $query->where('status', 'aktif');
                        })
                            ->orderBy('number')
                            ->select(['id', 'number', 'name AS k', 'sasaran_strategis_id'])
                            ->withCount([
                                'indikatorKinerja AS rowspan' => function (Builder $query) {
                                    $query->where('status', 'aktif');
                                }
                            ]);
                    },
                    'kegiatan.indikatorKinerja' => function (HasMany $query) use ($periodInstance) {
                        $query->where('status', 'aktif')
                            ->orderBy('number')
                            ->select(['id', 'type', 'number', 'name AS ik', 'kegiatan_id'])
                            ->withAggregate([
                                'realization AS realization' => function (Builder $query) use ($periodInstance) {
                                    $query->where('period_id', $periodInstance->id);
                                }
                            ], 'realization');
                    },
                ])
                ->orderBy('number')
                ->select(['id', 'number', 'name AS ss'])
                ->get()
                ->toArray();

            $data = array_map(function ($item) {
                return [
                    ...$item,
                    'rowspan' => array_sum(array_column($item['kegiatan'], 'rowspan')),
                ];
            }, $data);

            $badge = [
                $period === '1' ? 'Januari - Juni' : 'Juli - Desember',
                $year
            ];

            $periodId = $periodInstance->id;
        } else {
            $periods = [];

            $period = '';
            $year = '';

            $badge = [];
            $data = [];

            $periodId = '';
        }

        $status = [
            [
                'text' => 'Semua',
                'value' => '',
                // 'selected' => true,
            ],
            [
                'text' => 'Belum diisi',
                'value' => 'undone',
            ],
            [
                'text' => 'Sudah diisi',
                'value' => 'done',
            ],
        ];

        return view('admin.rs.home', compact([
            'periodId',
            'periods',
            'period',
            'status',
            'badge',
            'years',
            'year',
            'data'
        ]));
    }

    public function addAdmin(AddRequest $request, $periodId, $ikId)
    {
        $realization = $request["realization-$ikId"];

        $currentMonth = (int) Carbon::now()->format('m');
        $currentPeriod = $currentMonth < 6 ? '1' : '2';
        $currentYear = Carbon::now()->format('Y');

        $period = RSPeriod::whereKey($periodId)
            ->where('status', true)->whereHas('deadline', function (Builder $query) use ($currentPeriod, $currentYear) {
                $query->where('period', $currentPeriod)
                    ->whereHas('year', function (Builder $query) use ($currentYear) {
                        $query->where('year', $currentYear);
                    });
            })
            ->firstOrFail();

        $ik = IndikatorKinerja::whereKey($ikId)
            ->where('status', 'aktif')
            ->firstOrFail();

        if ($realization !== null && ($ik->type === 'persen' || $ik->type === 'angka')) {
            if (!is_numeric($realization)) {
                return back()
                    ->withInput()
                    ->withErrors(["realization-$ikId" => 'Realisasi tidak sesuai dengan tipe data']);
            }
        }

        $achievement = RSAchievement::where('unit_id', auth()->user()->unit->id)
            ->where('period_id', $period->id)
            ->where('indikator_kinerja_id', $ik->id)
            ->firstOrNew();

        $achievement->realization = $realization;

        $achievement->unit()->associate(auth()->user()->unit);
        $achievement->indikatorKinerja()->associate($ik);
        $achievement->period()->associate($period);

        $achievement->save();

        return back();
    }
}
