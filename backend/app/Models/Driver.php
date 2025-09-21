<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'current_shift_hours', 'past_7day_hours'];

    protected $casts = [
        'past_7day_hours' => 'array',
        'current_shift_hours' => 'float',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
