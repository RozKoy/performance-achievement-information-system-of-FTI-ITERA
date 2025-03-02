<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUAchievement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $table = 'iku_achievements';

    protected $fillable = [
        'status',
        'note',

        'indikator_kinerja_program_id',
        'period_id',
        'unit_id',
    ];


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function indikatorKinerjaProgram(): BelongsTo
    {
        return $this->belongsTo(IndikatorKinerjaProgram::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(IKUPeriod::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function data(): HasMany
    {
        return $this->hasMany(IKUAchievementData::class, 'achievement_id');
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    public function deleteOrTrashed(): void
    {
        $this->data()->forceDelete();

        $this->forceDelete();
    }
}
