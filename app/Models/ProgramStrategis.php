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

    protected $fillable = [
        'indikator_kinerja_kegiatan_id',
        'number',
        'name',
    ];

    public function indikatorKinerjaKegiatan(): BelongsTo
    {
        return $this->belongsTo(IndikatorKinerjaKegiatan::class);
    }

    public function indikatorKinerjaProgram(): HasMany
    {
        return $this->hasMany(IndikatorKinerjaProgram::class);
    }
}
