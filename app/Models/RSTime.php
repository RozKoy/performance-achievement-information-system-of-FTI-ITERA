<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RSTime extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_time';

    protected $fillable = [
        'status',
        'period',
        'year',
    ];

    public function sasaranStrategis(): HasMany
    {
        return $this->hasMany(SasaranStrategis::class, 'time_id');
    }

    public function deadline(): HasMany
    {
        return $this->hasMany(SasaranStrategis::class, 'deadline_id');
    }
}
