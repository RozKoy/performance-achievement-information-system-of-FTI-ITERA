<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kegiatan';

    protected $fillable = [
        'sasaran_strategis_id',
        'number',
        'name',
    ];

    public function sasaranStrategis(): BelongsTo
    {
        return $this->belongsTo(SasaranStrategis::class);
    }

    public function indikatorKinerja(): HasMany
    {
        return $this->hasMany(IndikatorKinerja::class);
    }

    public function deleteOrTrashed(): void
    {
        foreach ($this->indikatorKinerja as $key => $ik) {
            $ik->deleteOrTrashed();
        }

        $this->forceDelete();
    }
}
