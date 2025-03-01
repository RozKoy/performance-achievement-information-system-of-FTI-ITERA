<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaProgram extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $table = 'indikator_kinerja_program';

    protected $fillable = [
        'definition',
        'number',
        'status',
        'mode',
        'name',
        'type',

        'program_strategis_id',
    ];

    // 'mode' values
    public const MODE_SINGLE = 'single';
    public const MODE_TABLE = 'table';


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function programStrategis(): BelongsTo
    {
        return $this->belongsTo(ProgramStrategis::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function achievements(): HasMany
    {
        return $this->hasMany(IKUAchievement::class);
    }

    public function singleAchievements(): HasMany
    {
        return $this->hasMany(IKUSingleAchievement::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(IKPColumn::class);
    }

    public function target(): HasMany
    {
        return $this->hasMany(IKUTarget::class);
    }

    public function unitStatus(): HasMany
    {
        return $this->hasMany(IKUUnitStatus::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASONE
    | -----------------------------------------------------------------
    */

    public function evaluation(): HasOne
    {
        return $this->hasOne(IKUEvaluation::class);
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    public static function getModeValues(): array
    {
        return [
            self::MODE_SINGLE,
            self::MODE_TABLE,
        ];
    }

    public function deleteOrTrashed(): void
    {
        $this->singleAchievements()->forceDelete();
        $this->evaluation()->forceDelete();
        $this->target()->forceDelete();

        $this->achievements()->each(function (IKUAchievement $item): void {
            $item->deleteOrTrashed();
        });

        $this->columns()->forceDelete();

        $this->programStrategis->indikatorKinerjaProgram()
            ->where('number', '>', $this->number)
            ->decrement('number');

        $this->forceDelete();
    }
}
