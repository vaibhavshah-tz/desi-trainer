<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SubAdminRequest extends FormRequest
{
    public $rules = [];
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
        $this->rules['first_name'] = 'required';
        $this->rules['last_name'] = 'required';
        $this->rules['email'] = 'required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|unique:users,email,' . $this->id . ',id,deleted_at,NULL'; // /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/
        $this->rules['country_code'] = 'required';
        $this->rules['phone_number'] = 'required'; //regex:/^\d{8,15}$/
        $this->rules['full_phone_number'] = 'phone:AUTO';
        if ($this->id) {
            $this->rules['avatar'] = 'image|mimes:jpeg,png,jpg|max:5120';
        } else {
            $this->rules['password'] = 'required|min:6|same:password_confirmation|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
            $this->rules['password_confirmation'] = 'required';
        }

        return $this->rules;
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
            'email.unique' => __('Email is already exist'),
            'password.required' => __('Please enter password'),
            'password.min' => __('Please enter minimum 6 characters'),
            'password.regex' => __('Password must at least 1 lower character, at least 1 capital character, at least 1 numeric characte'),
            'password.same' => __('Password and confirm password does not match'),
            'password_confirmation.confirmed' => __('Password and confirm password does not match'),
            'password_confirmation.required' => __('Please enter confirm password'),
            'avatar.required' => __('Please select avatar'),
            'avatar.image' => __('Please select only image'),
            'avatar.mimes' => __('Only allowed file types: png, jpg, jpeg.'),
            'avatar.max' => __('File size must be less than 5MB'),
            'country_code.required' => __('Please select country code'),
            'phone_number.required' => __('Please enter contact number'),
            'phone_number.regex' => __('Allow only 8 to 15 numbers.'),
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
