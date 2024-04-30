<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SasaranStrategis extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'deadline_id',
        'time_id',
        'number',
        'name',
    ];
}
