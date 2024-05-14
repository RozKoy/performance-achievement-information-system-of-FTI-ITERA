<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class RSYear extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_years';

    protected $fillable = [
        'year',
    ];

    public function sasaranStrategis(): HasMany
    {
        return $this->hasMany(SasaranStrategis::class, 'time_id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(RSPeriod::class, 'year_id');
    }

    static function currentTime(): RSYear
    {
        $year = Carbon::now()->format('Y');

        return RSYear::firstOrCreate(['year' => $year]);
    }
}
