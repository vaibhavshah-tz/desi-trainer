<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserLogin extends FormRequest
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
            'email' => 'required|email',
            'password' => 'required',
            // 'device_token' => 'required',
            'device_type' => 'required'
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
            'email.required' => __('Please enter email'),
            'email.email' => __('Please enter valid email'),
            'password.required' => __('Please enter password'),
            'device_token.required' => __('Please enter device token'),
            'device_type.required' => __('Please enter device type'),
        ];
    }
}
