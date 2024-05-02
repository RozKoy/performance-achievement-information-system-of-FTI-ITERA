<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SasaranKegiatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'sasaran_kegiatan';

    protected $fillable = [
        'deadline_id',
        'time_id',
        'number',
        'name',
    ];
}
