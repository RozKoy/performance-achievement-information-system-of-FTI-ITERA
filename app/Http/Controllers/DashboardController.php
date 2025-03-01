<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\View\Factory;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Contracts\View\View;
use App\Exports\MultipleSheets;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Exports\IKUExport;
use App\Exports\RSExport;
use App\Models\IKUYear;
use App\Models\RSYear;
use App\Models\Unit;

class DashboardController extends Controller
{
    /**
     * Super admin dashboard
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function home(Request $request): Factory|View
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
            },
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.singleAchievements',
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.achievements',
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.unitStatus',
            'sasaranKegiatan.indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.target',
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
            },
            'sasaranStrategis.kegiatan.indikatorKinerja.textSelections',
            'sasaranStrategis.kegiatan.indikatorKinerja.realization',
            'sasaranStrategis.kegiatan.indikatorKinerja.target',
        ])->first();

        $units = Unit::latest()
            ->select([
                'short_name',
                'name',
                'id',
            ])
            ->get()
            ->toArray();

        $ikuIndikatorKinerjaProgram = collect();
        $iku = [
            'success' => $iku?->sasaranKegiatan?->sum(function ($item) use ($ikuIndikatorKinerjaProgram) {
                return $item->indikatorKinerjaKegiatan->sum(function ($item) use ($ikuIndikatorKinerjaProgram) {
                    $item->programStrategis->each(function ($item) use ($ikuIndikatorKinerjaProgram) {
                        $ikuIndikatorKinerjaProgram->push(...$item->indikatorKinerjaProgram);
                    });
                    return $item->programStrategis->sum('success');
                });
            }),
            'failed' => $iku?->sasaranKegiatan?->sum(function ($item) {
                return $item->indikatorKinerjaKegiatan->sum(function ($item) {
                    return $item->programStrategis->sum('failed');
                });
            }),
        ];

        $rsIndikatorKinerja = collect();
        $rs = [
            'success' => $rs?->sasaranStrategis?->sum(function ($item) use ($rsIndikatorKinerja) {
                $item->kegiatan->each(function ($item) use ($rsIndikatorKinerja) {
                    $rsIndikatorKinerja->push(...$item->indikatorKinerja);
                });
                return $item->kegiatan->sum('success');
            }),
            'failed' => $rs?->sasaranStrategis?->sum(function ($item) {
                return $item->kegiatan->sum('failed');
            }),
        ];

        $iku['sum'] = $iku['success'] + $iku['failed'];
        $rs['sum'] = $rs['success'] + $rs['failed'];

        $ikuPercent = $iku['sum'] ? $iku['success'] * 100 / $iku['sum'] : 0;
        $rsPercent = $rs['sum'] ? $rs['success'] * 100 / $rs['sum'] : 0;

        $ikuPercent = number_format((float) $ikuPercent, 2, '.', '');
        $rsPercent = number_format((float) $rsPercent, 2, '.', '');

        $rsIndikatorKinerja = $rsIndikatorKinerja->toArray();

        return view('super-admin.dashboard.home', compact([
            'ikuIndikatorKinerjaProgram',
            'rsIndikatorKinerja',
            'ikuYearList',
            'rsYearList',
            'ikuPercent',
            'rsPercent',
            'ikuYear',
            'rsYear',
            'units',
            'iku',
            'rs',
        ]));
    }

    /**
     * Super admin iku dashboard function
     * @param string $year
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function iku(string $year): Factory|View
    {
        $yearInstance = IKUYear::withTrashed()->where('year', $year)->firstOrFail();

        $datasets = collect();
        $idLists = collect();
        $data = $yearInstance->sasaranKegiatan()
            ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram')
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query) {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram')
                        ->select([
                            'name AS ikk',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerjaProgram')
                        ->select([
                            'name AS ps',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->select([
                        'name AS ikp',
                        'mode',
                        'id',

                        'program_strategis_id',
                    ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.singleAchievements',
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.achievements',
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram.target',
            ])
            ->select([
                'name AS sk',
                'id',
            ])
            ->orderBy('number')
            ->get();

        $units = Unit::withTrashed()
            ->where(function (Builder $query) use ($year) {
                $query->whereNotNull('deleted_at')->where(function (Builder $query) use ($year) {
                    $query->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year) {
                        $query->whereHas('period', function (Builder $query) use ($year) {
                            $query->whereHas('year', function (Builder $query) use ($year) {
                                $query->where('year', $year);
                            });
                        });
                    })
                        ->orWhereHas('indikatorKinerjaUtamaTarget', function (Builder $query) use ($year) {
                            $query->whereHas('indikatorKinerjaProgram', function (Builder $query) use ($year) {
                                $query->whereHas('programStrategis', function (Builder $query) use ($year) {
                                    $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($year) {
                                        $query->whereHas('sasaranKegiatan', function (Builder $query) use ($year) {
                                            $query->whereHas('time', function (Builder $query) use ($year) {
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


        $data->each(function ($item) use ($idLists, $datasets, $units) {
            $item->indikatorKinerjaKegiatan->each(function ($item) use ($idLists, $datasets, $units) {
                $item->programStrategis->each(function ($item) use ($idLists, $datasets, $units) {
                    $item->indikatorKinerjaProgram->each(function ($item) use ($idLists, $datasets, $units) {
                        $realizationTemp = collect();
                        $targetTemp = collect();
                        $unitTemp = collect();
                        $units->each(function ($unit) use ($realizationTemp, $targetTemp, $unitTemp, $item) {
                            if ($item->mode === 'table') {
                                $realizationTemp->push($item->achievements->where('unit_id', $unit->id)->count());
                            } else {
                                $realizationTemp->push($item->singleAchievements->where('unit_id', $unit->id)->average('value'));
                            }
                            $targetTemp->push($item->target->firstWhere('unit_id', $unit->id)?->target ?? 0);
                            $unitTemp->push($unit->short_name);
                        });
                        $datasets->put($item->id, [
                            'realization' => $realizationTemp->toArray(),
                            'target' => $targetTemp->toArray(),
                            'unit' => $unitTemp->toArray(),
                        ]);
                        $idLists->push($item->id);
                    });
                });
            });
        });

        $ikuYearList = IKUYear::orderBy('year')
            ->pluck('year')
            ->map(function ($item) use ($year) {
                if ($item === $year) {
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

        $previousRoute = route('super-admin-dashboard', ['ikuYear' => $year]);

        return view('super-admin.dashboard.iku', compact([
            'previousRoute',
            'ikuYearList',
            'datasets',
            'idLists',
            'data',
        ]));
    }

    /**
     * Super admin iku excel export function
     * @param string $year
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportIKU(string $year): BinaryFileResponse
    {
        $yearInstance = IKUYear::withTrashed()->where('year', $year)->firstOrFail();

        $data = $yearInstance->sasaranKegiatan()
            ->whereHas('indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram')
            ->with([
                'indikatorKinerjaKegiatan' => function (HasMany $query) {
                    $query->whereHas('programStrategis.indikatorKinerjaProgram')
                        ->select([
                            'name',
                            'id',

                            'sasaran_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerjaProgram')
                        ->select([
                            'name',
                            'id',

                            'indikator_kinerja_kegiatan_id',
                        ])
                        ->orderBy('number');
                },
                'indikatorKinerjaKegiatan.programStrategis.indikatorKinerjaProgram' => function (HasMany $query) {
                    $query->with([
                        'singleAchievements',
                        'achievements',
                        'evaluation',
                        'target',
                    ])
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
            ->where(function (Builder $query) use ($year) {
                $query->whereNotNull('deleted_at')->where(function (Builder $query) use ($year) {
                    $query->whereHas('indikatorKinerjaUtama', function (Builder $query) use ($year) {
                        $query->whereHas('period', function (Builder $query) use ($year) {
                            $query->whereHas('year', function (Builder $query) use ($year) {
                                $query->where('year', $year);
                            });
                        });
                    })
                        ->orWhereHas('indikatorKinerjaUtamaTarget', function (Builder $query) use ($year) {
                            $query->whereHas('indikatorKinerjaProgram', function (Builder $query) use ($year) {
                                $query->whereHas('programStrategis', function (Builder $query) use ($year) {
                                    $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($year) {
                                        $query->whereHas('sasaranKegiatan', function (Builder $query) use ($year) {
                                            $query->whereHas('time', function (Builder $query) use ($year) {
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
                                        ->where('unit_id', $unit->id)
                                        ->whereHas('period', function ($query) use ($period) {
                                            $query->whereIn('period', $period);
                                        })
                                        ->count();
                                } else {
                                    $temp[] = $ikp->singleAchievements()
                                        ->where('unit_id', $unit->id)
                                        ->whereHas('period', function ($query) use ($period) {
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
            new MultipleSheets(IKUExport::class, $names, $collection->toArray()),
            'indikator-kinerja-utama.xlsx'
        );
    }

    /**
     * Super admin rs excel export function
     * @param string $year
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportRS(string $year): BinaryFileResponse
    {
        $yearInstance = RSYear::withTrashed()->where('year', $year)->firstOrFail();

        $data = $yearInstance->sasaranStrategis()
            ->whereHas('kegiatan.indikatorKinerja')
            ->with([
                'kegiatan' => function (HasMany $query) {
                    $query->whereHas('indikatorKinerja')
                        ->select([
                            'name',
                            'id',

                            'sasaran_strategis_id',
                        ])
                        ->orderBy('number');
                },
                'kegiatan.indikatorKinerja' => function (HasMany $query) {
                    $query->with([
                        'realization',
                        'target',
                    ])
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
            ->where(function (Builder $query) use ($year) {
                $query->whereNotNull('deleted_at')->where(function (Builder $query) use ($year) {
                    $query->whereHas('rencanaStrategis', function (Builder $query) use ($year) {
                        $query->whereHas('period', function (Builder $query) use ($year) {
                            $query->whereHas('year', function (Builder $query) use ($year) {
                                $query->where('year', $year);
                            });
                        });
                    })
                        ->orWhereHas('rencanaStrategisTarget', function (Builder $query) use ($year) {
                            $query->whereHas('indikatorKinerja', function (Builder $query) use ($year) {
                                $query->whereHas('kegiatan', function (Builder $query) use ($year) {
                                    $query->whereHas('sasaranStrategis', function (Builder $query) use ($year) {
                                        $query->whereHas('time', function (Builder $query) use ($year) {
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
                            $temp[] = $ik->target->firstWhere('unit_id', $unit->id)?->target ?? '';
                            if ($ik->type === 'teks') {
                                $temp[] = $ik->realization()
                                    ->where('unit_id', $unit->id)
                                    ->whereHas('period', function ($query) use ($period) {
                                        $query->whereIn('period', $period);
                                    })
                                    ->get()
                                    ->implode('realization', ', ');
                            } else if ($ik->type === 'angka') {
                                $temp[] = $ik->realization()
                                    ->where('unit_id', $unit->id)
                                    ->whereHas('period', function ($query) use ($period) {
                                        $query->whereIn('period', $period);
                                    })
                                    ->count();
                            } else if ($ik->type === 'persen') {
                                $temp[] = $ik->realization()
                                    ->where('unit_id', $unit->id)
                                    ->whereHas('period', function ($query) use ($period) {
                                        $query->whereIn('period', $period);
                                    })
                                    ->average('realization');
                            }
                        }

                        $item->add($temp);
                    }
                }
            }
        }

        return Excel::download(
            new MultipleSheets(RSExport::class, $names, $collection->toArray()),
            'rencana-strategis.xlsx'
        );
    }
}
