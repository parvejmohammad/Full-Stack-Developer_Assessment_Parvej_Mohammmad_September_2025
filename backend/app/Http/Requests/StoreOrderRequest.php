<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'order_code' => 'required|string|max:100|unique:orders,order_code',
            'delivery_route_id' => 'required|exists:delivery_routes,id',
            'value_rs' => 'required|numeric|min:0',
            'driver_id' => 'nullable|exists:drivers,id',
            'delivered_at' => 'nullable|date',
        ];
    }
}
