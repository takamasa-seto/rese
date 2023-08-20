<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddReviewRequest extends FormRequest
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
            'star' => ['required', 'numeric', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:400'],
            'image_file' => ['nullable', 'file', 'mimes:jpeg,jpg,png', 'dimensions:min_width=100,min_height=100,max_width=2000,max_width=2000']
        ];
    }
}
