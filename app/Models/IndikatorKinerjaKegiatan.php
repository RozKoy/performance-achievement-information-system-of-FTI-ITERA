<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaKegiatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'indikator_kinerja_kegiatan';

    protected $fillable = [
        'sasaran_kegiatan_id',
        'number',
        'name',
    ];

    public function sasaranKegiatan(): BelongsTo
    {
        return $this->belongsTo(SasaranKegiatan::class);
    }
}
