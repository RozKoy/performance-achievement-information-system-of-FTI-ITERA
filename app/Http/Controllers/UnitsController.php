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
            $users = User::findMany($request->safe()['users']);

            foreach ($users as $key => $user) {
                $user->unit()->associate($unit);
                $user->save();
            }
        }

        return redirect()->route('super-admin-unit');
    }

    public function editView($id)
    {
        $unit = Unit::whereKey($id)
            ->with('users')
            ->firstOrFail(['id', 'name', 'short_name']);

        $usersExists = $unit->users()
            ->select(['id', 'name AS username', 'email', 'access'])
            ->selectRaw('true AS checked')
            ->get()
            ->toArray();
        $usersList = User::where('role', 'admin')
            ->whereNull('unit_id')
            ->get(['id', 'name AS username', 'email', 'access'])
            ->toArray();

        $users = array_merge($usersExists, $usersList);

        $data = $unit->toArray();

        return view('super-admin.unit.edit', compact(['data', 'users']));
    }

    public function edit(EditRequest $request, $id)
    {
        $unit = Unit::findOrFail($id);

        $newName = $request->safe()['name'];

        if ($unit->name !== $newName) {
            $unit->name = $newName;
        }

        $unit->short_name = $request->safe()['short_name'];
        $unit->save();

        $oldUsers = [];
        $newUsers = [];

        if (isset($request->safe()['users'])) {
            if (isset($request->safe()['users']['old'])) {
                $oldUsers = $request->safe()['users']['old'];
            }
            if (isset($request->safe()['users']['new'])) {
                $newUsers = $request->safe()['users']['new'];
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

    public function delete($id)
    {
        $unit = Unit::findOrFail($id);

        $achievements = $unit->rencanaStrategis()
            ->whereNotNull('period_id')
            ->get();
        $currentYear = Carbon::now()->format('Y');
        $old = false;

        foreach ($achievements as $key => $achievement) {
            $year = $achievement->period->year;

            if ($year->year !== $currentYear) {
                $old = true;
            } else {
                $ik = $achievement->indikatorKinerja;

                $ik->realization()
                    ->whereBelongsTo($unit)
                    ->whereNull('period_id')
                    ->forceDelete();

                if ($ik->type !== 'teks' && $ik->status === 'aktif') {
                    $allAchievement = $ik->realization()
                        ->whereNull(['period_id', 'unit_id'])
                        ->first();
                    $periodAchievement = $ik->realization()
                        ->whereBelongsTo($achievement->period, 'period')
                        ->whereNull('unit_id')
                        ->first();

                    foreach ([$periodAchievement, $allAchievement] as $key => $achievementInstance) {
                        if ($achievementInstance) {
                            $realization = (float) $achievementInstance->realization || 0;
                            $unitRealization = (float) $achievement->realization || 0;

                            if ($ik->type === 'angka') {
                                $realization -= $unitRealization;
                            } else {
                                if ($achievementInstance->period) {
                                    $count = $ik->realization()
                                        ->whereBelongsTo($achievementInstance->period, 'period')
                                        ->whereNotNull('unit_id')
                                        ->count();
                                } else {
                                    $count = $ik->realization()
                                        ->whereNotNull(['period_id', 'unit_id'])
                                        ->count();
                                }

                                $realization *= $count;
                                $realization -= $unitRealization;

                                if ($count > 2) {
                                    $realization /= ($count - 1);
                                }
                            }

                            $achievementInstance->realization = "$realization";
                            $achievementInstance->save();
                        }
                    }
                }

                $achievement->forceDelete();
            }
        }

        User::where('unit_id', $id)
            ->update(['unit_id' => null]);

        if ($old) {
            $unit->delete();
        } else {
            $unit->forceDelete();
        }

        return back();
    }
}
