<?php

namespace App\Http\Controllers\Admin\IndikatorKinerjaUtama;

use App\Http\Controllers\SuperAdmin\IndikatorKinerjaUtama\HomeIndikatorKinerjaUtamaSuperAdminController;
use App\Http\Requests\IndikatorKinerjaUtama\AddTableDataRequest;
use App\Http\Controllers\_ControllerHelpers;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\IKUAchievementData;
use Illuminate\Http\UploadedFile;
use App\Models\IKUAchievement;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class AddTableDataIndikatorKinerjaUtamaAdminController extends Controller
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param string $period
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function addBulkData(Request $request, string $period, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        if ($ikp->status !== 'aktif' || $ikp->mode !== 'table') {
            abort(404);
        }

        $user = auth()->user();

        $columns = $ikp->columns()
            ->orderBy('number')
            ->get();

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentDate = Carbon::now();

        $periodInstance = $year->periods()
            ->whereDate('deadline', '>=', $currentDate)
            ->where('period', $period)
            ->where('status', true)
            ->firstOrFail();

        foreach ($request['old'] ?? [] as $itemKey => $item) {
            $data = $item['data'] ?? [];
            $id = $item['id'] ?? null;

            if ($id && count($data)) {
                if ($achievement = $ikp->achievements()->whereKey($item['id'])->where('status', true)->first()) {
                    foreach ($data as $colId => $value) {
                        if ($column = $columns->firstWhere('id', $colId)) {
                            if ($value !== null) {
                                $temp = $achievement->data()->firstOrNew([
                                    'column_id' => $column->id,
                                ]);

                                $temp->data = $value;

                                $temp->save();
                            } else {
                                $achievement->data()->where('column_id', $column->id)->forceDelete();
                            }
                        }
                    }

                    if ($temp = $columns->firstWhere('file', true)) {
                        if (isset($request->file('old')[$itemKey]['data'][$temp->id])) {
                            if ($request->file('old')[$itemKey]['data'][$temp->id] instanceof UploadedFile) {
                                $file = $achievement->data()->firstOrNew(
                                    [
                                        'column_id' => $temp->id,
                                    ],
                                );

                                if ($file->data) {
                                    if (Storage::exists($file->data)) {
                                        Storage::delete($file->data);
                                    }
                                }

                                $fileURI = $request->file('old')[$itemKey]['data'][$temp->id]
                                    ->store('IKUFiles/' . $user->unit->name . '/' . $ikp->id);

                                $file->data = $fileURI;
                                $file->save();
                            }
                        }
                    }
                }
            }
        }

        $unset = $ikp->achievements()
            ->whereIn('id', $request['delete'] ?? [])
            ->whereBelongsTo($periodInstance, 'period')
            ->whereBelongsTo($user->unit)
            ->where('status', true)
            ->get();

        foreach ($unset as $item) {
            $itemData = $item->data()
                ->whereHas('column', function (Builder $query): void {
                    $query->where('file', true);
                })
                ->get();

            foreach ($itemData as $cItem) {
                if (Storage::exists($cItem->data)) {
                    Storage::delete($cItem->data);
                }
            }

            $item->deleteOrTrashed();
        }

        $achievementInsertData = [];
        $achievementDataInsertData = [];
        foreach ($request['new'] ?? [] as $itemKey => $item) {
            $inputExists = false;
            foreach ($columns->where('file', false) as $key => $column) {
                if ($item[(string) $column->id] !== null) {
                    $inputExists = true;
                    break;
                }
            }

            if ($inputExists) {
                $achievementID = uuid_create();

                $achievementInsertData[] = [
                    'id' => $achievementID,

                    'indikator_kinerja_program_id' => $ikp->id,
                    'period_id' => $periodInstance->id,
                    'unit_id' => $user->unit->id,

                    'created_at' => $currentDate,
                    'updated_at' => $currentDate,
                ];

                foreach ($columns->where('file', false) as $key => $column) {
                    $value = $item[(string) $column->id];
                    if ($value !== null) {
                        $achievementDataInsertData[] = [
                            'id' => uuid_create(),

                            'data' => $value,

                            'achievement_id' => $achievementID,
                            'column_id' => $column->id,

                            'created_at' => $currentDate,
                            'updated_at' => $currentDate,
                        ];
                    }
                }

                if ($temp = $columns->firstWhere('file', true)) {
                    if (isset($request->file('new')[$itemKey][$temp->id])) {
                        if ($request->file('new')[$itemKey][$temp->id] instanceof UploadedFile) {
                            $fileURI = $request->file('new')[$itemKey][$temp->id]
                                ->store('IKUFiles/' . $user->unit->name . '/' . $ikp->id);

                            $achievementDataInsertData[] = [
                                'id' => uuid_create(),

                                'data' => $fileURI,

                                'achievement_id' => $achievementID,
                                'column_id' => $temp->id,

                                'created_at' => $currentDate,
                                'updated_at' => $currentDate,
                            ];
                        }
                    }
                }
            }
        }

        if (count($achievementInsertData)) {
            IKUAchievement::insert($achievementInsertData);
            IKUAchievementData::insert($achievementDataInsertData);
        }

        $evaluation = $ikp->evaluation;

        if ($evaluation) {
            $all = $ikp->achievements()->where('status', true)->count();

            $evaluation->status = $all >= $evaluation->target;
            $evaluation->save();
        }

        $ikp->unitStatus()
            ->whereBelongsTo($periodInstance, 'period')
            ->whereBelongsTo($user->unit, 'unit')
            ->forceDelete();

        return _ControllerHelpers::Back()->with('success', 'Berhasil memperbarui data tabel');
    }

    /**
     * @param \App\Http\Requests\IndikatorKinerjaUtama\AddTableDataRequest $request
     * @param string $period
     * @param \App\Models\IndikatorKinerjaProgram $ikp
     * @return RedirectResponse
     */
    public function addData(AddTableDataRequest $request, string $period, IndikatorKinerjaProgram $ikp): RedirectResponse
    {
        HomeIndikatorKinerjaUtamaSuperAdminController::CheckRoutine();

        if ($ikp->status !== 'aktif' || $ikp->mode !== 'table') {
            abort(404);
        }

        $user = auth()->user();

        $columns = $ikp->columns()
            ->orderBy('number')
            ->get();

        $inputExists = false;
        foreach ($columns->where('file', false) as $key => $column) {
            if ($request["data-$column->id"] !== null) {
                $inputExists = true;
                break;
            }
        }

        if (!$inputExists) {
            return _ControllerHelpers::BackWithInputWithErrors(['input' => 'Data yang dimasukkan tidak boleh kosong semua']);
        }

        $ps = $ikp->programStrategis;
        $ikk = $ps->indikatorKinerjaKegiatan;
        $sk = $ikk->sasaranKegiatan;

        $year = $sk->time;

        $currentDate = Carbon::now();

        $periodInstance = $year->periods()
            ->whereDate('deadline', '>=', $currentDate)
            ->where('period', $period)
            ->where('status', true)
            ->firstOrFail();

        $achievement = $ikp->achievements()->create([
            'period_id' => $periodInstance->id,
            'unit_id' => $user->unit->id,
        ]);

        $columns->where('file', false)->each(function ($column) use ($achievement, $request): void {
            if ($input = $request["data-$column->id"]) {
                $achievement->data()->create([
                    'data' => $input,

                    'column_id' => $column->id,
                ]);
            }
        });

        $fileColumn = $columns->firstWhere('file', true);

        if ($fileColumn !== null) {
            $name = "file-$fileColumn->id";
            if ($request->hasFile($name)) {
                $fileURI = $request->file($name)
                    ->store('IKUFiles/' . $user->unit->name . '/' . $ikp->id);

                $achievement->data()->create([
                    'data' => $fileURI,

                    'column_id' => $fileColumn->id,
                ]);
            }
        }

        $evaluation = $ikp->evaluation;

        if ($evaluation) {
            $all = $ikp->achievements()->where('status', true)->count();

            $evaluation->status = $all >= $evaluation->target;
            $evaluation->save();
        }

        $ikp->unitStatus()
            ->whereBelongsTo($periodInstance, 'period')
            ->whereBelongsTo($user->unit, 'unit')
            ->forceDelete();

        return _ControllerHelpers::Back()->with('success', 'Berhasil menambahkan data tabel');
    }
}
