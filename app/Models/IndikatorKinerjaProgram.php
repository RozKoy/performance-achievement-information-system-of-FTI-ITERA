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
        'name',
        'type',

        'program_strategis_id',
    ];


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

    public function columns(): HasMany
    {
        return $this->hasMany(IKPColumn::class);
    }

    public function target(): HasMany
    {
        return $this->hasMany(IKUTarget::class);
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

    public function deleteOrTrashed(): void
    {
        $this->achievements()->forceDelete();
        $this->evaluation()->forceDelete();
        $this->columns()->forceDelete();
        $this->target()->forceDelete();

        $this->programStrategis->indikatorKinerjaProgram()
            ->where('number', '>', $this->number)
            ->decrement('number');

        $this->forceDelete();
    }
}
