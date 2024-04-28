<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'kegiatan';

    protected $fillable = [
        'sasaran_strategis_id',
        'number',
        'name',
    ];
}
