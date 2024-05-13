<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class IKUYear extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_years';

    protected $fillable = [
        'year',
    ];

    public function sasaranKegiatan(): HasMany
    {
        return $this->hasMany(SasaranKegiatan::class, 'time_id');
    }

    static function currentTime(): IKUYear
    {
        $year = Carbon::now()->format('Y');

        return IKUYear::firstOrCreate(['year' => $year]);
    }
}
