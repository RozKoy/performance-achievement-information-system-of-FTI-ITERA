<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ProgramStrategis extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $fillable = [
        'number',
        'name',

        'indikator_kinerja_kegiatan_id',
    ];


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function indikatorKinerjaKegiatan(): BelongsTo
    {
        return $this->belongsTo(IndikatorKinerjaKegiatan::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function indikatorKinerjaProgram(): HasMany
    {
        return $this->hasMany(IndikatorKinerjaProgram::class);
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    public function deleteOrTrashed(): void
    {
        foreach ($this->indikatorKinerjaProgram as $key => $ikp) {
            $ikp->deleteOrTrashed();
        }

        $this->indikatorKinerjaKegiatan->programStrategis()
            ->where('number', '>', $this->number)
            ->decrement('number');

        $this->forceDelete();
    }
}
