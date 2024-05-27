<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKPColumn extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'ikp_columns';

    protected $fillable = [
        'image',
        'name',

        'indikator_kinerja_program_id',
    ];
}
