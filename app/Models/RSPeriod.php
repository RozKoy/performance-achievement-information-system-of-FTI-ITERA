<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RSPeriod extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_periods';

    protected $fillable = [
        'deadline_id',
        'year_id',
        'period',
        'status',
    ];

    public function year(): BelongsTo
    {
        return $this->belongsTo(RSYear::class);
    }

    public function periods(): HasMany
    {
        return $this->hasMany(RSPeriod::class, 'deadline_id');
    }

    public function deadline(): BelongsTo
    {
        return $this->belongsTo(RSPeriod::class);
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(RSAchievement::class, 'period_id');
    }
}
