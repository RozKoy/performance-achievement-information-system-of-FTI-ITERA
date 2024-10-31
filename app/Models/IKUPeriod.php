<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUPeriod extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $table = 'iku_periods';

    protected $fillable = [
        'period',
        'status',

        'deadline_id',
        'year_id',
    ];


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function deadline(): BelongsTo
    {
        return $this->belongsTo(IKUPeriod::class);
    }

    public function year(): BelongsTo
    {
        return $this->belongsTo(IKUYear::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function achievements(): HasMany
    {
        return $this->hasMany(IKUAchievement::class, 'period_id');
    }

    public function singleAchievements(): HasMany
    {
        return $this->hasMany(IKUSingleAchievement::class, 'period_id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(IKUPeriod::class, 'deadline_id');
    }
}
