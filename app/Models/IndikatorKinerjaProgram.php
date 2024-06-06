<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaProgram extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'indikator_kinerja_program';

    protected $fillable = [
        'program_strategis_id',
        'definition',
        'number',
        'status',
        'type',
        'name',
    ];

    public function programStrategis(): BelongsTo
    {
        return $this->belongsTo(ProgramStrategis::class);
    }

    public function columns(): HasMany
    {
        return $this->hasMany(IKPColumn::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(IKUAchievement::class);
    }
}
