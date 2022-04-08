<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifyOtp extends FormRequest
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
            'otp' => 'required',
            'user_id' => 'required',
            'user_type' => 'required'
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
            'otp.required' => __('Please enter otp'),
            'user_id.required' => __('Please enter user id'),
            'user_type.required' => __('Please enter user type'),
        ];
    }
}
