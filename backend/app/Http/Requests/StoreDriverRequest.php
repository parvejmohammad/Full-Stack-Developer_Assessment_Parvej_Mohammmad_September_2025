<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDriverRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'current_shift_hours' => 'nullable|numeric|min:0',
            'past_7day_hours' => 'nullable|array|max:7',
            'past_7day_hours.*' => 'numeric|min:0',
        ];
    }
}
