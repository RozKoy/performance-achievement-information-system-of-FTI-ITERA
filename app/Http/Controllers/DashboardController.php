<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\IKUYear;
use App\Models\RSYear;

class DashboardController extends Controller
{
    /**
     * Super admin dashboard
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function superAdmin(): Factory|View
    {
        $rs = RSYear::with([
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
        ])
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                $success = $item->sasaranStrategis->sum(function ($item) {
                    return $item->kegiatan->sum('success');
                });
                $failed = $item->sasaranStrategis->sum(function ($item) {
                    return $item->kegiatan->sum('failed');
                });

                return [
                    'year' => $item->year,
                    'success' => $success,
                    'failed' => $failed,
                ];
            });

        $iku = IKUYear::with([
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
        ])
            ->orderBy('year')
            ->get()
            ->map(function ($item) {
                $success = $item->sasaranKegiatan->sum(function ($item) {
                    return $item->indikatorKinerjaKegiatan->sum(function ($item) {
                        return $item->programStrategis->sum('success');
                    });
                });
                $failed = $item->sasaranKegiatan->sum(function ($item) {
                    return $item->indikatorKinerjaKegiatan->sum(function ($item) {
                        return $item->programStrategis->sum('failed');
                    });
                });

                return [
                    'year' => $item->year,
                    'success' => $success,
                    'failed' => $failed,
                ];
            });

        return view('super-admin.home', compact([
            'iku',
            'rs',
        ]));
    }
}
