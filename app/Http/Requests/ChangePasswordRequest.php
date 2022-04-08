<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'old_password' => 'required',
            'new_password' => 'required|min:6|same:password_confirmation|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required',
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
            'old_password.required' => __('Please enter password'),
            'new_password.min' => __('Please enter minimum 6 characters'),
            'new_password.regex' => __('Password must at least 1 lower character, at least 1 capital character, at least 1 numeric characte'),
            'password_confirmation.same' => __('Password and confirm password does not match'),
            'password_confirmation.required' => __('Please enter confirm password'),

        ];
    }
}
