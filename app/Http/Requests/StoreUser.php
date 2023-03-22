<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUser extends FormRequest
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
            'id' => 'exists:App\Models\HT20\User,id|max:20',
            'name' => 'required|max:24',
            'position' => 'required|max:24',
            'apartment_id' => 'required|numeric',
            'location' => 'required|max:24',
            'skype' => 'max:50',
            'email_htauto' => 'max:50',
            'phone_htauto' => 'max:11',
            'phone' => 'max:11',
            'birth_day' => 'min:7|max:12',
        ];
    }
}
