<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $fillable = [
        'short_name',
        'name',
    ];


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function indikatorKinerjaUtamaTarget(): HasMany
    {
        return $this->hasMany(IKUTarget::class);
    }

    public function rencanaStrategisTarget(): HasMany
    {
        return $this->hasMany(RSTarget::class);
    }

    public function IKUStatus(): HasMany
    {
        return $this->hasMany(IKUUnitStatus::class);
    }

    public function indikatorKinerjaUtama(): HasMany
    {
        return $this->hasMany(IKUAchievement::class);
    }

    public function singleIndikatorKinerjaUtama(): HasMany
    {
        return $this->hasMany(IKUSingleAchievement::class);
    }

    public function rencanaStrategis(): HasMany
    {
        return $this->hasMany(RSAchievement::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
