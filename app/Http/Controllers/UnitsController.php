<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use App\Http\Requests\Units\EditRequest;
use App\Http\Requests\Units\AddRequest;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\User;

class UnitsController extends Controller
{
    public function homeView(Request $request)
    {
        $data = Unit::where(function (Builder $query) use ($request) {
            if ($request->search) {
                $query->whereAny(
                    [
                        'short_name',
                        'name'
                    ],
                    'LIKE',
                    "%{$request->search}%"
                );
            }
        })
            ->select([
                'short_name',
                'name',
                'id',
            ])
            ->withCount('users AS users')
            ->latest()
            ->get()
            ->toArray();

        return view('super-admin.unit.home', compact('data'));
    }

    public function addView()
    {
        $users = User::where('role', 'admin')
            ->doesntHave('unit')
            ->select([
                'name AS username',
                'access',
                'email',
                'id',
            ])
            ->get()
            ->toArray();

        return view('super-admin.unit.add', compact('users'));
    }

    public function add(AddRequest $request)
    {
        $unitExists = Unit::withTrashed()
            ->firstWhere('name', $request['name']);

        if ($unitExists) {
            if ($unitExists->deleted_at) {
                $unitExists->restore();
            } else {
                return back()
                    ->withInput()
                    ->withErrors(['name' => 'Nama unit sudah digunakan']);
            }

            $unit = $unitExists;
        } else {
            $unit = Unit::create($request->safe()->only('short_name', 'name'));
        }

        if (isset($request->safe()['users'])) {
            $users = User::findMany($request['users']);

            foreach ($users as $key => $user) {
                $user->unit()->associate($unit);
                $user->save();
            }
        }

        return redirect()->route('super-admin-unit');
    }

    public function editView(Unit $unit)
    {
        $usersList = User::doesntHave('unit')
            ->where('role', 'admin')
            ->select([
                'name AS username',
                'access',
                'email',
                'id',
            ])
            ->get();

        $users = User::whereBelongsTo($unit)
            ->select([
                'name AS username',
                'access',
                'email',
                'id',
            ])
            ->selectRaw('true AS checked')
            ->get()
            ->merge($usersList)
            ->toArray();

        $data = $unit->only([
            'short_name',
            'name',
            'id',
        ]);

        return view('super-admin.unit.edit', compact([
            'users',
            'data',
        ]));
    }

    public function edit(EditRequest $request, Unit $unit)
    {
        $newName = $request['name'];

        if ($unit->name !== $newName) {
            $unit->name = $newName;
        }

        $unit->short_name = $request['short_name'];
        $unit->save();

        $oldUsers = [];
        $newUsers = [];

        if (isset($request['users'])) {
            if (isset($request['users']['old'])) {
                $oldUsers = $request['users']['old'];
            }
            if (isset($request['users']['new'])) {
                $newUsers = $request['users']['new'];
            }
        }

        $unit->users()->each(function ($user) use ($oldUsers) {
            if (!in_array($user->id, $oldUsers)) {
                $user->unit()->dissociate();
                $user->save();
            }
        });

        $users = User::findMany($newUsers);

        foreach ($users as $key => $user) {
            $user->unit()->associate($unit);
            $user->save();
        }

        return redirect()->route('super-admin-unit');
    }

    public function delete(Unit $unit)
    {
        $currentYear = Carbon::now()->format('Y');

        $RSAchievements = $unit->rencanaStrategis()
            ->whereHas('period', function (Builder $query) use ($currentYear) {
                $query->whereHas('year', function (Builder $query) use ($currentYear) {
                    $query->where('year', $currentYear);
                });
            })
            ->get();

        foreach ($RSAchievements as $key => $RSAchievement) {
            $ik = $RSAchievement->indikatorKinerja;

            $ik->realization()
                ->whereBelongsTo($unit)
                ->whereNull('period_id')
                ->forceDelete();

            $RSAchievement->forceDelete();

            if ($ik->type !== 'teks' && $ik->status === 'aktif') {
                $allAchievement = $ik->realization()
                    ->whereNull(['period_id', 'unit_id'])
                    ->first();
                $periodAchievement = $ik->realization()
                    ->whereBelongsTo($RSAchievement->period, 'period')
                    ->whereNull('unit_id')
                    ->first();

                foreach ([$periodAchievement, $allAchievement] as $key => $achievementInstance) {
                    if ($achievementInstance) {
                        $realization = $ik->realization()
                            ->where(function (Builder $query) use ($achievementInstance) {
                                $query->whereNotNull('unit_id');
                                if ($achievementInstance->period) {
                                    $query->whereBelongsTo($achievementInstance->period, 'period');
                                } else {
                                    $query->whereNotNull('period_id');
                                }
                            })
                            ->get();

                        $sumRealization = $realization->sum('realization');

                        if ($ik->type === 'persen') {
                            $count = $realization->count();

                            if ($count) {
                                $sumRealization /= $count;
                            }
                        }

                        $achievementInstance->realization = "$sumRealization";
                        $achievementInstance->save();
                    }
                }
            }
        }

        $RSTargets = $unit->rencanaStrategisTarget()
            ->whereHas('indikatorKinerja', function (Builder $query) use ($currentYear) {
                $query->whereHas('kegiatan', function (Builder $query) use ($currentYear) {
                    $query->whereHas('sasaranStrategis', function (Builder $query) use ($currentYear) {
                        $query->whereHas('time', function (Builder $query) use ($currentYear) {
                            $query->where('year', $currentYear);
                        });
                    });
                });
            })
            ->get();

        foreach ($RSTargets as $key => $RSTarget) {
            $ik = $RSTarget->indikatorKinerja;
            $evaluation = $ik->evaluation;

            $RSTarget->forceDelete();

            if ($evaluation !== null && $ik->type !== 'teks' && $ik->status === 'aktif') {
                $sumTarget = $ik->target->sum('target');

                if ($ik->type === 'persen') {
                    $count = $ik->target->count();

                    if ($count) {
                        $sumTarget /= $count;
                    }
                }

                $allAchievement = $ik->realization()
                    ->whereNull(['period_id', 'unit_id'])
                    ->first();

                $evaluation->target = "$sumTarget";
                $evaluation->status = false;

                if ($allAchievement) {
                    $evaluation->status = (float) $allAchievement->realization >= $sumTarget;
                }

                $evaluation->save();
            }
        }

        $IKUAchievements = $unit->indikatorKinerjaUtama()
            ->whereHas('period', function (Builder $query) use ($currentYear) {
                $query->whereHas('year', function (Builder $query) use ($currentYear) {
                    $query->where('year', $currentYear);
                });
            })
            ->get();

        foreach ($IKUAchievements as $key => $IKUAchievement) {
            $IKUAchievement->data()->forceDelete();
            $IKUAchievement->forceDelete();
        }

        $IKUTargets = $unit->indikatorKinerjaUtamaTarget()
            ->whereHas('indikatorKinerjaProgram', function (Builder $query) use ($currentYear) {
                $query->whereHas('programStrategis', function (Builder $query) use ($currentYear) {
                    $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($currentYear) {
                        $query->whereHas('sasaranKegiatan', function (Builder $query) use ($currentYear) {
                            $query->whereHas('time', function (Builder $query) use ($currentYear) {
                                $query->where('year', $currentYear);
                            });
                        });
                    });
                });
            })
            ->get();

        foreach ($IKUTargets as $key => $IKUTarget) {
            $ikp = $IKUTarget->indikatorKinerjaProgram;
            $evaluation = $ikp->evaluation;

            $IKUTarget->forceDelete();

            if ($evaluation) {
                $realization = $ikp->achievements->count();
                $sumTarget = $ikp->target->sum('target');

                $evaluation->target = $sumTarget;
                $evaluation->status = $realization >= $sumTarget;

                $evaluation->save();
            }
        }

        $RSOldAchievements = $unit->rencanaStrategis()
            ->whereHas('period', function (Builder $query) use ($currentYear) {
                $query->whereHas('year', function (Builder $query) use ($currentYear) {
                    $query->whereNot('year', $currentYear);
                });
            })
            ->exists();

        $RSOldTargets = $unit->rencanaStrategisTarget()
            ->whereHas('indikatorKinerja', function (Builder $query) use ($currentYear) {
                $query->whereHas('kegiatan', function (Builder $query) use ($currentYear) {
                    $query->whereHas('sasaranStrategis', function (Builder $query) use ($currentYear) {
                        $query->whereHas('time', function (Builder $query) use ($currentYear) {
                            $query->whereNot('year', $currentYear);
                        });
                    });
                });
            })
            ->exists();

        $IKUOldAchievements = $unit->indikatorKinerjaUtama()
            ->whereHas('period', function (Builder $query) use ($currentYear) {
                $query->whereHas('year', function (Builder $query) use ($currentYear) {
                    $query->whereNot('year', $currentYear);
                });
            })
            ->exists();

        $IKUOldTargets = $unit->indikatorKinerjaUtamaTarget()
            ->whereHas('indikatorKinerjaProgram', function (Builder $query) use ($currentYear) {
                $query->whereHas('programStrategis', function (Builder $query) use ($currentYear) {
                    $query->whereHas('indikatorKinerjaKegiatan', function (Builder $query) use ($currentYear) {
                        $query->whereHas('sasaranKegiatan', function (Builder $query) use ($currentYear) {
                            $query->whereHas('time', function (Builder $query) use ($currentYear) {
                                $query->whereNot('year', $currentYear);
                            });
                        });
                    });
                });
            })
            ->exists();

        User::where('unit_id', $unit->id)
            ->update(['unit_id' => null]);

        if ($RSOldAchievements || $RSOldTargets || $IKUOldAchievements || $IKUOldTargets) {
            $unit->delete();
        } else {
            $unit->forceDelete();
        }

        return back();
    }
}
