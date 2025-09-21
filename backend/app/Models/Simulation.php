<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = ['available_drivers', 'route_start_time', 'max_hours_per_driver', 'inputs', 'results'];

    protected $casts = [
        'inputs' => 'array',
        'results' => 'array',
    ];
}
