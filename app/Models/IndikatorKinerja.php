<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerja extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'indikator_kinerja';

    protected $fillable = [
        'kegiatan_id',
        'status',
        'number',
        'type',
        'name',
    ];

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function realization(): HasMany
    {
        return $this->hasMany(RSAchievement::class);
    }

    public function evaluation(): HasOne
    {
        return $this->hasOne(RSEvaluation::class);
    }
}
