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

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $table = 'iku_years';

    protected $fillable = [
        'year',
    ];


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function sasaranKegiatan(): HasMany
    {
        return $this->hasMany(SasaranKegiatan::class, 'time_id');
    }

    public function periods(): HasMany
    {
        return $this->hasMany(IKUPeriod::class, 'year_id');
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    public static function currentTime(): IKUYear
    {
        $year = Carbon::now()->format('Y');

        return IKUYear::firstOrCreate(['year' => $year]);
    }
}
