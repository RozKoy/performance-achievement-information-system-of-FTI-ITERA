<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUUnitStatus extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_unit_statuses';

    protected $fillable = [
        'status',

        'indikator_kinerja_program_id',
        'period_id',
        'unit_id',
    ];

    // 'status' values
    public const STATUS_BLANK = 'blank';


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function indikatorKinerjaProgram(): BelongsTo
    {
        return $this->belongsTo(IndikatorKinerjaProgram::class);
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(IKUPeriod::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    public static function getStatusValues(): array
    {
        return [
            self::STATUS_BLANK,
        ];
    }
}
