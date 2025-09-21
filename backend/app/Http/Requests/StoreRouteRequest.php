<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouteRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'route_code' => 'required|string|max:100|unique:delivery_routes,route_code',
            'distance_km' => 'required|numeric|min:0',
            'traffic_level' => 'required|in:Low,Medium,High',
            'base_time_minutes' => 'required|integer|min:1',
        ];
    }
}
