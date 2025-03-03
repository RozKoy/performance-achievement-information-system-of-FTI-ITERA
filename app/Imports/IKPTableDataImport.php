<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\IndikatorKinerjaProgram;
use Illuminate\Support\Collection;
use App\Models\IKUAchievementData;
use Illuminate\Support\Carbon;
use App\Models\IKUAchievement;

class IKPTableDataImport implements ToCollection
{
    public function __construct(public IndikatorKinerjaProgram $ikp, public string $period_id, public string $unit_id)
    {
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection): void
    {
        $columns = $this->ikp->columns()
            ->where('file', false)
            ->orderBy('number')
            ->get();

        $currentDate = Carbon::now();

        $achievementInsertData = [];
        $achievementDataInsertData = [];
        foreach ($collection as $itemKey => $item) {
            if ($itemKey > 0) {
                $achievementID = uuid_create();

                $temp = [];
                foreach ($columns as $columnKey => $column) {
                    $value = $item[$columnKey] ?? null;

                    if ($value !== null) {
                        $temp[] = [
                            'id' => uuid_create(),

                            'data' => $value,

                            'achievement_id' => $achievementID,
                            'column_id' => $column->id,

                            'created_at' => $currentDate,
                            'updated_at' => $currentDate,
                        ];
                    }
                }

                if (count($temp)) {
                    $achievementInsertData[] = [
                        'id' => $achievementID,

                        'indikator_kinerja_program_id' => $this->ikp->id,
                        'period_id' => $this->period_id,
                        'unit_id' => $this->unit_id,

                        'created_at' => $currentDate,
                        'updated_at' => $currentDate,
                    ];

                    $achievementDataInsertData = [
                        ...$achievementDataInsertData,
                        ...$temp,
                    ];
                }
            }
        }

        if (count($achievementInsertData)) {
            IKUAchievement::insert($achievementInsertData);
            IKUAchievementData::insert($achievementDataInsertData);
        }
    }
}
