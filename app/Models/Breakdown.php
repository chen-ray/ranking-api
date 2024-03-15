<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Breakdown extends Model
{
    use HasFactory;

    protected $fillable = ['ranking_id', 'ranking_calc', 'date', 'name', 'result', 'points', 'url'];

}
