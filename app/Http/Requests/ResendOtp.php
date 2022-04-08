<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendOtp extends FormRequest
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
            'user_id' => 'required',
            'user_type' => 'required|in:1,2',
        ];
    }

    /**
     * Get the validation rules messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_id.required' => __('Please enter user id'),
            'user_type.required' => __('Please enter user type'),
            'user_type.in' => __('Please enter valid user type'),
        ];
    }
}
