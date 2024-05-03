<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class SasaranKegiatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sasaran_kegiatan';

    protected $fillable = [
        'deadline_id',
        'time_id',
        'number',
        'name',
    ];

    public function time(): BelongsTo
    {
        return $this->belongsTo(IKUTime::class);
    }

    public function deadline(): BelongsTo
    {
        return $this->belongsTo(IKUTime::class);
    }

    public function indikatorKinerjaKegiatan(): HasMany
    {
        return $this->hasMany(IndikatorKinerjaKegiatan::class);
    }

    static function currentOrFail($id): SasaranKegiatan
    {
        $ss = SasaranKegiatan::findOrFail($id);

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

        if ($ss->time->year === $year && $ss->time->period === $period) {
            return $ss;
        }

        return abort(404);
    }
}
