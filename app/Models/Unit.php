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

    protected $fillable = [
        'short_name',
        'name',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function rencanaStrategis(): HasMany
    {
        return $this->hasMany(RSAchievement::class);
    }

    public function rencanaStrategisTarget(): HasMany
    {
        return $this->hasMany(RSTarget::class);
    }

    public function indikatorKinerjaUtama(): HasMany
    {
        return $this->hasMany(IKUAchievement::class);
    }

    public function indikatorKinerjaUtamaTarget(): HasMany
    {
        return $this->hasMany(IKUTarget::class);
    }
}
