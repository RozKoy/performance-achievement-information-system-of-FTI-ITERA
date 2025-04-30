<?php

namespace App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use App\Models\IKUSingleAchievement;
use Illuminate\Contracts\View\View;
use App\Models\IKUAchievement;
use Illuminate\Http\Request;

class DetailIndikatorKinerjaUtamaSuperAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return Factory|View
     */
    public function view(Request $request, IndikatorKinerjaProgram $ikp): Factory|View
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $periodQuery = $request->query('period');

        if (isset($periodQuery) && !in_array($periodQuery, ['1', '2', '3', '4', '5'])) {
            abort(404);
        }

        $user = auth()->user();

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
            ->get()
            ->toArray();

        $achievement = 0;
        $dataCount = 0;

        $data = collect();

        if ($ikp->mode === 'table') {
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
                ->whereBelongsTo($ikp)
                ->select([
                    'status',
                    'note',
                    'id',
                ])
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();

            $achievement = $data->where('status', true)->count();
            $dataCount = $data->count();
        } else {
            $data = IKUSingleAchievement::withTrashed()
                ->where(function (Builder $query) use ($periodInstance): void {
                    if ($periodInstance) {
                        $query->whereBelongsTo($periodInstance, 'period');
                    }
                })
                ->whereBelongsTo($ikp)
                ->select([
                    'value',
                    'link',
                    'id',
                ])
                ->withAggregate('unit AS unit', 'name')
                ->latest()
                ->get();

            $achievement = $data->average('value');
            if (!ctype_digit(text: (string) $achievement) && $achievement) {
                $achievement = number_format((float) $achievement, 2);
            }
        }

        $data = $ikp->status === 'aktif' ? $data->groupBy('unit')->toArray() : $data->toArray();

        $evaluation = $ikp->evaluation;

        $sk = $sk->only([
            'number',
            'name',
        ]);

        $ikk = $ikk->only([
            'number',
            'name',
        ]);

        $ps = $ps->only([
            'number',
            'name',
        ]);

        $ikp = $ikp->only([
            'definition',
            'number',
            'status',
            'mode',
            'name',
            'type',
            'id',
        ]);

        $badge = [
            $periods->firstWhere('value', $period)['title'],
            $year
        ];

        $periods = $periods->toArray();

        return view('super-admin.achievement.iku.detail', compact([
            'achievement',
            'evaluation',
            'dataCount',
            'columns',
            'periods',
            'period',
            'badge',
            'data',
            'user',
            'year',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }
}
