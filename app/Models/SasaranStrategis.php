<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SasaranStrategis extends Model
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

        'time_id',
    ];


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function time(): BelongsTo
    {
        return $this->belongsTo(RSYear::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY THROUGH
    | -----------------------------------------------------------------
    */

    public function indikatorKinerja(): HasManyThrough
    {
        return $this->hasManyThrough(IndikatorKinerja::class, Kegiatan::class);
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    static function currentOrFail($id): SasaranStrategis
    {
        $ss = SasaranStrategis::findOrFail($id);
        $year = Carbon::now()->format('Y');

        if ($ss->time->year === $year) {
            return $ss;
        }

        abort(404);
    }

    public function deleteOrTrashed(): void
    {
        foreach ($this->kegiatan as $key => $k) {
            $k->deleteOrTrashed();
        }

        $this->time->sasaranStrategis()
            ->where('number', '>', $this->number)
            ->decrement('number');

        $this->forceDelete();
    }
}
