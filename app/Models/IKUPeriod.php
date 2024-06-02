<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUPeriod extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_periods';

    protected $fillable = [
        'period',
        'status',

        'deadline_id',
        'year_id',
    ];

    public function year(): BelongsTo
    {
        return $this->belongsTo(IKUYear::class);
    }
}
