<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IndikatorKinerjaProgram extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'indikator_kinerja_program';

    protected $fillable = [
        'definition',
        'column',
        'number',
        'type',
        'name',
    ];
}
