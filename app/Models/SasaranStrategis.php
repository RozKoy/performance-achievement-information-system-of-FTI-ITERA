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

    protected $fillable = [
        'time_id',
        'number',
        'name',
    ];

    public function time(): BelongsTo
    {
        return $this->belongsTo(RSYear::class);
    }

    public function kegiatan(): HasMany
    {
        return $this->hasMany(Kegiatan::class);
    }

    public function indikatorKinerja(): HasManyThrough
    {
        return $this->hasManyThrough(IndikatorKinerja::class, Kegiatan::class);
    }

    static function currentOrFail($id): SasaranStrategis
    {
        $ss = SasaranStrategis::findOrFail($id);

        $year = Carbon::now()->format('Y');

        if ($ss->time->year === $year) {
            return $ss;
        }

        return abort(404);
    }

    public function deleteOrTrashed(): void
    {
        foreach ($this->kegiatan as $key => $k) {
            $k->deleteOrTrashed();
        }

        $this->forceDelete();
    }
}
