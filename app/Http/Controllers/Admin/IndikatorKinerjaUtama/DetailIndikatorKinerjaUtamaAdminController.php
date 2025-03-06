<?php

namespace App\Http\Controllers\Admin\IndikatorKinerjaUtama;

use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\HomeIndikatorKinerjaUtamaSuperAdminController;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Contracts\View\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DetailIndikatorKinerjaUtamaAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return Factory|View
     */
    public function view(Request $request, IndikatorKinerjaProgram $ikp): Factory|View
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        $periodRequest = $request->query('period');

        if ($ikp->status !== 'aktif') {
            abort(404);
        }
        if ($periodRequest !== null && !in_array($periodRequest, ['1', '2', '3', '4'])) {
            abort(404);
        }

        $user = auth()->user();

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentDate = Carbon::now();

        $currentMonth = (int) $currentDate->format('m');
        $currentYear = $currentDate->format('Y');
        $currentPeriod = '1';

        foreach ([3, 6, 9, 12] as $key => $value) {
            if ($currentMonth <= $value) {
                $currentPeriod = (string) ($key + 1);
                break;
            }
        }

        $periods = $year->periods()
            ->whereDate('deadline', '>=', $currentDate)
            ->where('status', true)
            ->orderBy('period')
            ->pluck('period')
            ->map(function ($item): array {
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
                    'value' => $item,
                ];
            });

        if (!$periods->count()) {
            abort(404);
        }

        $period = $periodRequest ?? $periods->last()['value'];
        $periodInstance = $year->periods()
            ->whereDate('deadline', '>=', $currentDate)
            ->where('period', $period)
            ->where('status', true)
            ->firstOrFail();

        $realization = null;
        $columns = [];
        $data = [];

        if ($ikp->mode === 'table') {
            $columns = $ikp->columns()
                ->select([
                    'file',
                    'name',
                    'id'
                ])
                ->orderBy('number')
                ->get()
                ->toArray();

            $data = $periodInstance->achievements()
                ->with('data', function (HasMany $query): void {
                    $query->select([
                        'data',

                        'achievement_id',
                        'column_id',
                    ])
                        ->withAggregate('column AS file', 'file');
                })
                ->whereBelongsTo($user->unit)
                ->whereBelongsTo($ikp)
                ->select([
                    'status',
                    'note',
                    'id',
                ])
                ->orderBy('created_at')
                ->get()
                ->toArray();

            $realization = $ikp->achievements()
                ->whereBelongsTo($user->unit)
                ->where('status', true)
                ->count();
        } else {
            $data = $periodInstance->singleAchievements()
                ->whereBelongsTo($user->unit)
                ->whereBelongsTo($ikp)
                ->select([
                    'value',
                    'link',
                ])
                ->first()?->toArray();

            $realization = $ikp->singleAchievements()
                ->whereBelongsTo($user->unit)
                ->average('value');

            if (!ctype_digit((string) $realization)) {
                $realization = number_format($realization, 2);
            }
        }

        $unitStatus = $ikp->unitStatus()
            ->whereBelongsTo($periodInstance, 'period')
            ->whereBelongsTo($user->unit, 'unit')
            ->first()?->status ?? null;
        $target = $ikp->target()
            ->whereBelongsTo($user->unit)
            ->first()?->target ?? null;

        $sk = $sk->only([
            'number',
            'name',
            'id',
        ]);
        $ikk = $ikk->only([
            'number',
            'name',
            'id',
        ]);
        $ps = $ps->only([
            'number',
            'name',
            'id',
        ]);
        $ikp = $ikp->only([
            'definition',
            'number',
            'mode',
            'name',
            'type',
            'id',
        ]);

        $badge = [$periods->firstWhere('value', $period)['title'], $year->year];
        $periods = $periods->toArray();

        return view('admin.iku.detail', compact([
            'realization',
            'unitStatus',
            'columns',
            'periods',
            'period',
            'target',
            'badge',
            'data',
            'user',
            'ikk',
            'ikp',
            'ps',
            'sk',
        ]));
    }
}
