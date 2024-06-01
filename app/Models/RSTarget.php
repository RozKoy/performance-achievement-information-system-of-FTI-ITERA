<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RSTarget extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_targets';

    protected $fillable = [
        'target',

        'indikator_kinerja_id',
        'unit_id',
    ];
}
