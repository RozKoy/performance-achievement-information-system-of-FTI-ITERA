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
        $year = Carbon::now()->format('Y');

        return IKUTime::firstOrCreate(['year' => $year], ['status' => 'aktif']);
    }
}
