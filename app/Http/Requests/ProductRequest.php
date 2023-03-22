<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
            'name' => 'required|min:5',
            'description' => 'required',
            // 'image' => 'required',
        ];
    }

    public function message()
    {
        return [
            'name.required' => 'The title is required ',
            'description.required' => 'The description is required',
            // 'image.required'            => 'This is required',
        ];
    }
}
