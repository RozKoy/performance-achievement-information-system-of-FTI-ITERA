<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class IKUAchievementData extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = '';

    protected $fillable = [
        'data',

        'achievement_id',
        'column_id',
    ];
}
