<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryRoute extends Model
{
    use HasFactory;

    protected $table = 'delivery_routes';

    protected $fillable = ['route_code', 'distance_km', 'traffic_level', 'base_time_minutes'];

    protected $casts = [
        'distance_km' => 'float',
        'base_time_minutes' => 'integer',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class, 'delivery_route_id');
    }
}
