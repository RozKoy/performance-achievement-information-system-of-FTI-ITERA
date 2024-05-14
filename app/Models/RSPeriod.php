<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RSPeriod extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'rs_periods';

    protected $fillable = [
        'deadline_id',
        'year_id',
        'period',
        'status',
    ];
}
