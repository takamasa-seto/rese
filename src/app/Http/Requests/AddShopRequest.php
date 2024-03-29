<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'region' => ['required', 'string', 'max:255'],
            'genre' => ['required', 'string', 'max:255'],
            'operation_pattern' => ['required', 'numeric', 'between:0,255'],
            'time_per_reservation' => ['required', 'date_format:H:i'],
            'tables' => ['required', 'array'],
            'tables.*' => ['required', 'numeric', 'between:0,255'],
            'image_file' => ['file', 'mimes:jpeg, jpg, png', 'dimensions:min_width=100,min_height=100,max_width=2000,max_width=2000'],
        ];
    }
}
