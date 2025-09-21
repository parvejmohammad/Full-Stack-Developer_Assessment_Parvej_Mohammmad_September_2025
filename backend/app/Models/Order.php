<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'order_code',
    'delivery_route_id',
    'value_rs',
    'driver_id',
    'delivery_minutes'
];

protected $casts = [
    'value_rs' => 'float',
    'delivery_minutes' => 'integer',
];

    public function route()
    {
        return $this->belongsTo(DeliveryRoute::class, 'delivery_route_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}
