<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RSAchievement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_achievements';

    protected $fillable = [
        'indikator_kinerja_id',
        'period_id',
        'unit_id',

        'realization',
    ];
}
