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

    protected $table = 'iku_achievements';

    protected $fillable = [
        'indikator_kinerja_program_id',
        'period_id',
        'unit_id',
    ];

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

    public function data(): HasMany
    {
        return $this->hasMany(IKUAchievementData::class, 'achievement_id');
    }
}
