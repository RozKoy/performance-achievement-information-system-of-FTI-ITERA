<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUAchievement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_achievements';

    protected $fillable = [
        'indikator_kinerja_program_id',
        'period_id',
        'unit_id',
    ];
}
