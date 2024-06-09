<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUEvaluation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_evaluations';

    protected $fillable = [
        'evaluation',
        'follow_up',
        'status',
        'target',

        'indikator_kinerja_program_id',
    ];

    public function indikatorKinerjaProgram(): BelongsTo
    {
        return $this->belongsTo(IndikatorKinerjaProgram::class);
    }
}
