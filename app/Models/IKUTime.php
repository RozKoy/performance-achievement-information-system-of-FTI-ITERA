<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class IKUTime extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_time';

    protected $fillable = [
        'status',
        'period',
        'year',
    ];

    public function sasaranKegiatan(): HasMany
    {
        return $this->hasMany(SasaranKegiatan::class, 'time_id');
    }

    public function deadline(): HasMany
    {
        return $this->hasMany(SasaranKegiatan::class, 'deadline_id');
    }

    static function currentTime(): IKUTime
    {
        $month = (int) Carbon::now()->format('m');
        $year = Carbon::now()->format('Y');
        $period = '1';
        if ($month > 9) {
            $period = '4';
        } else if ($month > 6) {
            $period = '3';
        } else if ($month > 3) {
            $period = '2';
        }

        return IKUTime::firstOrCreate(['period' => $period, 'year' => $year], ['status' => 'aktif']);
    }
}
