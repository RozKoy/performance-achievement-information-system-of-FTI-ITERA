<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\IKUYear;
use App\Models\RSYear;
use Mockery\Undefined;

class DashboardController extends Controller
{
    /**
     * Super admin dashboard
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function superAdmin(Request $request): Factory|View
    {
        $ikuYear = $request->query('ikuYear') ?? strval(Carbon::now()->year);
        $rsYear = $request->query('rsYear') ?? strval(Carbon::now()->year);

        $ikuYearList = IKUYear::orderBy('year')
            ->pluck('year')
            ->map(function ($item) use ($ikuYear) {
                if ($item === $ikuYear) {
                    return [
                        'selected' => true,
                        'value' => $item,
                        'text' => $item,
                    ];
                }
                return [
                    'value' => $item,
                    'text' => $item,
                ];
            })
            ->toArray();

        $rsYearList = RSYear::orderBy('year')
            ->pluck('year')
            ->map(function ($item) use ($rsYear) {
                if ($item === $rsYear) {
                    return [
                        'selected' => true,
                        'value' => $item,
                        'text' => $item,
                    ];
                }
                return [
                    'value' => $item,
                    'text' => $item,
                ];
            })
            ->toArray();

        $iku = IKUYear::where('year', $ikuYear)->with([
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                $query->whereHas('indikatorKinerjaProgram')
                    ->withCount([
                        'indikatorKinerjaProgram AS success' => function (Builder $query) {
                            $query->whereHas('evaluation', function (Builder $query) {
                                $query->where('status', true);
                            });
                        },
                        'indikatorKinerjaProgram AS failed' => function (Builder $query) {
                            $query->whereDoesntHave('evaluation')
                                ->orWhereHas('evaluation', function (Builder $query) {
                                    $query->where('status', false);
                                });
                        },
                    ]);
            }
        ])->first();

        $rs = RSYear::where('year', $rsYear)->with([
            'sasaranStrategis.kegiatan' => function (HasMany $query) {
                $query->whereHas('indikatorKinerja')
                    ->withCount([
                        'indikatorKinerja AS success' => function (Builder $query) {
                            $query->whereHas('evaluation', function (Builder $query) {
                                $query->where('status', true);
                            });
                        },
                        'indikatorKinerja AS failed' => function (Builder $query) {
                            $query->whereDoesntHave('evaluation')
                                ->orWhereHas('evaluation', function (Builder $query) {
                                    $query->where('status', false);
                                });
                        },
                    ]);
            }
        ])->first();

        $iku = [
            'success' => $iku?->sasaranKegiatan?->sum(function ($item) {
                return $item->indikatorKinerjaKegiatan->sum(function ($item) {
                    return $item->programStrategis->sum('success');
                });
            }) ?? 0,
            'failed' => $iku?->sasaranKegiatan?->sum(function ($item) {
                return $item->indikatorKinerjaKegiatan->sum(function ($item) {
                    return $item->programStrategis->sum('failed');
                });
            }) ?? 0,
        ];

        $rs = [
            'success' => $rs?->sasaranStrategis?->sum(function ($item) {
                return $item->kegiatan->sum('success');
            }) ?? 0,
            'failed' => $rs?->sasaranStrategis?->sum(function ($item) {
                return $item->kegiatan->sum('failed');
            }) ?? 0,
        ];

        $ikuSum = $iku['success'] + $iku['failed'];
        $rsSum = $rs['success'] + $rs['failed'];

        $ikuPercent = $ikuSum ? $iku['success'] * 100 / $ikuSum : 0;
        $rsPercent = $rsSum ? $rs['success'] * 100 / $rsSum : 0;

        $ikuPercent = number_format((float) $ikuPercent, 2, '.', '');
        $rsPercent = number_format((float) $rsPercent, 2, '.', '');

        return view('super-admin.home', compact([
            'ikuYearList',
            'rsYearList',
            'ikuPercent',
            'rsPercent',
            'ikuSum',
            'rsSum',
            'iku',
            'rs',
        ]));
    }
}
