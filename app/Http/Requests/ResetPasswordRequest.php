<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'new_password' => 'required|min:6|same:password_confirmation|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'password_confirmation' => 'required',
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
            'new_password.min' => __('Please enter minimum 6 characters'),
            'new_password.regex' => __('Password must at least 1 lower character, at least 1 capital character, at least 1 numeric characte'),
            'password_confirmation.same' => __('Password and confirm password does not match'),
            'password_confirmation.required' => __('Please enter confirm password'),
            'user_type.required' => __('Please enter user type'),
            'user_type.in' => __('Please enter valid user type'),
        ];
    }
}
