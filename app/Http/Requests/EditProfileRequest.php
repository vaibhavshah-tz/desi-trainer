<?php

namespace App\Http\Requests;

use Auth;
use Illuminate\Foundation\Http\FormRequest;

class EditProfileRequest extends FormRequest
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
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|unique:users,email,".Auth::id().",id,deleted_at,NULL",
            'country_code' => 'required',
            'phone_number' => 'required',
            'full_phone_number' => 'phone:AUTO',
            'avatar' => 'image|mimes:jpeg,png,jpg|max:5120'
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
            'first_name.required' => __('Please enter first name'),
            'last_name.required' => __('Please enter last name'),
            'email.required' => __('Please enter email'),
            'email.email' => __('Please enter valid email'),
            'email.regex' => __('Email format is not valid'),
            'email.unique' => __('Email already exists'),
            'country_code.required' => __('Please select country code'),
            'phone_number.required' => __('Please enter contact number'),
            'avatar.image' => __('Please select only image'),
            'avatar.mimes' => __('Only allowed file types: png, jpg, jpeg.'),
            'avatar.max' => __('File size must be less than 5MB'),
            'full_phone_number.phone' => __('Please enter valid number.'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'full_phone_number' => $this->country_code.$this->phone_number,
        ]);
    }
}
