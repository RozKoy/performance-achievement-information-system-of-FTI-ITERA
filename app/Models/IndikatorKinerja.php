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

    /*
    | -----------------------------------------------------------------
    | VARIABLES
    | -----------------------------------------------------------------
    */

    protected $table = 'indikator_kinerja';

    protected $fillable = [
        'number',
        'status',
        'name',
        'type',

        'kegiatan_id',
    ];

    // 'type' values
    public const TYPE_PERCENT = 'persen';
    public const TYPE_NUMBER = 'angka';
    public const TYPE_TEXT = 'teks';


    /*
    | -----------------------------------------------------------------
    | RELATION - BELONGSTO
    | -----------------------------------------------------------------
    */

    public function kegiatan(): BelongsTo
    {
        return $this->belongsTo(Kegiatan::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASMANY
    | -----------------------------------------------------------------
    */

    public function realization(): HasMany
    {
        return $this->hasMany(RSAchievement::class);
    }

    public function target(): HasMany
    {
        return $this->hasMany(RSTarget::class);
    }

    public function textSelections(): HasMany
    {
        return $this->hasMany(IndikatorKinerjaTextSelection::class);
    }


    /*
    | -----------------------------------------------------------------
    | RELATION - HASONE
    | -----------------------------------------------------------------
    */

    public function evaluation(): HasOne
    {
        return $this->hasOne(RSEvaluation::class);
    }


    /*
    | -----------------------------------------------------------------
    | FUNCTION
    | -----------------------------------------------------------------
    */

    public static function getTypeValues(): array
    {
        return [
            self::TYPE_PERCENT,
            self::TYPE_NUMBER,
            self::TYPE_TEXT,
        ];
    }

    public function deleteOrTrashed(): void
    {
        $this->realization()->forceDelete();
        $this->evaluation()->forceDelete();
        $this->target()->forceDelete();

        $this->kegiatan->indikatorKinerja()
            ->where('number', '>', $this->number)
            ->decrement('number');

        $this->textSelections()->forceDelete();
        $this->forceDelete();
    }
}
