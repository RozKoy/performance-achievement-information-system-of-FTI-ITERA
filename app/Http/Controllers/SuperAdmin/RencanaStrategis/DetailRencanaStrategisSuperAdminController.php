<?php

namespace App\Http\Controllers\SuperAdmin\RencanaStrategis;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use App\Models\IndikatorKinerja;
use Illuminate\Http\Request;
use App\Models\Unit;

class DetailRencanaStrategisSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerja $ik
     * @return Factory|View
     */
    public function view(Request $request, IndikatorKinerja $ik): Factory|View
    {
        HomeRencanaStrategisSuperAdminController::CheckRoutine();

        $periodQuery = $request->query('period');

        if (isset($periodQuery) && !in_array($periodQuery, ['1', '2', '3'])) {
            abort(404);
        }

        $user = auth()->user();

        $k = $ik->kegiatan;
        $ss = $k->sasaranStrategis;

        $yearInstance = $ss->time;
        $year = $yearInstance->year;

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
            });

        if ($periods->count() === 2) {
            $periods->push([
                'title' => 'Januari - Desember',
                'value' => '3'
            ]);
        }

        $period = $periodQuery ?? $periods->last()['value'];

        if ((int) $period > $periods->count()) {
            abort(404);
        }
        $periods = $periods->toArray();

        $periodInstance = $yearInstance->periods()
            ->where('period', $period)
            ->first();

        $data = $ik->realization()
            ->with('unit', function (BelongsTo $query) use ($ik): void {
                $query->withTrashed()
                    ->select([
                        'name',
                        'id',
                    ])
                    ->withAggregate([
                        'rencanaStrategisTarget AS target' => function (Builder $query) use ($ik): void {
                            $query->whereBelongsTo($ik);
                        }
                    ], 'target');
            })
            ->where(function (Builder $query) use ($periodInstance): void {
                $query->whereNotNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNull('period_id');
                }
            })
            ->select([
                'unit_id',

                'realization',
                'link',
            ])
            ->latest()
            ->get()
            ->toArray();

        $realization = $ik->realization()
            ->where(function (Builder $query) use ($periodInstance): void {
                $query->whereNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNull('period_id');
                }
            })
            ->first();

        if ($realization) {
            $realization = $realization->realization;
        }

        $status = [
            [
                'text' => 'Tidak tercapai',
                'value' => 0,
            ],
            [
                'text' => 'Tercapai',
                'value' => 1,
            ],
        ];

        $evaluation = $ik->evaluation;
        if ($evaluation) {
            $status[$evaluation->status] = [
                ...$status[$evaluation->status],
                'selected' => true,
            ];

            $evaluation = $evaluation->only([
                'evaluation',
                'follow_up',
                'status',
                'target',
            ]);
        }

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

        $realizationCount = $ik->realization()
            ->where(function (Builder $query) use ($periodInstance): void {
                $query->whereNotNull('unit_id');
                if ($periodInstance) {
                    $query->whereBelongsTo($periodInstance, 'period');
                } else {
                    $query->whereNotNull('period_id');
                }
            })
            ->count();

        $badge = [
            $period === '3' ? 'Januari - Desember' : ($period === '2' ? 'Juli - Desember' : 'Januari - Juni'),
            $year
        ];

        $textSelections = $ik->textSelections;
        $textRealization = [
            [
                'text' => 'Pilih Realisasi',
                'value' => '',
            ],
            ...$textSelections->map(function ($selection) use ($realization): array {
                $temp = [
                    'text' => $selection['value'],
                    'value' => $selection['id'],
                ];
                if ($temp['value'] === $realization) {
                    $temp = [...$temp, 'selected' => true];
                }
                return $temp;
            }),
        ];
        $textTarget = [
            [
                'text' => 'Pilih Target',
                'value' => '',
            ],
            ...$textSelections->map(function ($selection) use ($evaluation): array {
                $temp = [
                    'text' => $selection['value'],
                    'value' => $selection['id'],
                ];
                if ($evaluation) {
                    if ($temp['value'] === $evaluation['target']) {
                        $temp = [...$temp, 'selected' => true];
                    }
                }
                return $temp;
            }),
        ];

        $ss = $ss->only([
            'number',
            'name',
        ]);
        $k = $k->only([
            'number',
            'name',
        ]);
        $ik = $ik->only([
            'number',
            'status',
            'name',
            'type',
            'id',
        ]);

        return view('super-admin.achievement.rs.detail', compact([
            'realizationCount',
            'textRealization',
            'textSelections',
            'realization',
            'evaluation',
            'textTarget',
            'unitCount',
            'periods',
            'period',
            'status',
            'badge',
            'data',
            'user',
            'year',
            'ss',
            'ik',
            'k',
        ]));
    }
}
