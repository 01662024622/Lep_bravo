<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportMarket extends FormRequest
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
            'customer_id' => 'exists:App\Models\HT20\B20Customer,id|max:20',
            'advisory' => 'required|max:255',
            'feedback' => 'max:255',
            'feedback_other' => 'max:255',
            'dev_plan' => 'max:255',
            'type' => 'required',
            'scale' => 'required',
            'service' => 'max:255',
            'type_market' => 'max:255',
            'user_id' => 'exists:App\Models\HT20\User,id|max:20',
            'date_work' => 'min:7|max:12',
            'image_1' => 'mimes:jpeg,bmp,png,jpg',
            'image_2' => 'mimes:jpeg,bmp,png,jpg',
            'image_3' => 'mimes:jpeg,bmp,png,jpg',
        ];
    }
}
