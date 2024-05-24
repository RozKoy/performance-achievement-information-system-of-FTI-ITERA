<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RSEvaluation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_evaluations';

    protected $fillable = [
        'evaluation',
        'follow_up',
        'status',
        'target',

        'indikator_kinerja_id',
    ];

    public function indikatorKinerja(): BelongsTo
    {
        return $this->belongsTo(IndikatorKinerja::class);
    }
}
