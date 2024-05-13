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
        'time_id',
        'number',
        'name',
    ];

    public function time(): BelongsTo
    {
        return $this->belongsTo(IKUYear::class);
    }

    public function indikatorKinerjaKegiatan(): HasMany
    {
        return $this->hasMany(IndikatorKinerjaKegiatan::class);
    }

    static function currentOrFail($id): SasaranKegiatan
    {
        $ss = SasaranKegiatan::findOrFail($id);

        $year = Carbon::now()->format('Y');

        if ($ss->time->year === $year) {
            return $ss;
        }

        return abort(404);
    }
}
