<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUTarget extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'iku_targets';

    protected $fillable = [
        'target',

        'indikator_kinerja_program_id',
        'unit_id',
    ];
}
