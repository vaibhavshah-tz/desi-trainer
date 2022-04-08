<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreCustomer extends FormRequest
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
        $method = Request::method();
        if (!in_array($method, ['PATCH', 'PUT'])) {
            if (!$this->id) {
                $this->rules['password'] = 'required|min:6|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
                $this->rules['password_confirmation'] = 'required';
            }
        }
        $this->rules['first_name'] = 'required';
        $this->rules['last_name'] = 'required';
        $this->rules['gender'] = 'required|in:1,2';
        $this->rules['company_name'] = 'required_if:user_type,2';
        // $this->rules['company_address'] = 'required_if:user_type,2';
        // $this->rules['username'] = 'required|min:6|unique:customers,username,' . $this->id;
        $this->rules['user_type'] = 'required|in:1,2';
        $this->rules['country_id'] = 'required';
        $this->rules['timezone_id'] = 'required';
        $this->rules['city'] = 'required';
        $this->rules['zipcode'] = 'required|max:10';
        $this->rules['avatar'] = 'image|mimes:jpeg,png,jpg|max:5120';

        if (Request::is('api/*')) {
            $id = !empty(\Auth::guard('customer')->user()) ? \Auth::guard('customer')->user()->id : $this->id;
            $this->rules['email'] = 'required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|unique:customers,email,' . $id . ',id,deleted_at,NULL';
            $this->rules['country_code'] = 'required';
            $this->rules['phone_number'] = 'required'; //regex:/^\d{8,15}$/
            $this->rules['full_phone_number'] = 'phone:AUTO';
        } else {
            if(!$this->id || \Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
                $this->rules['country_code'] = 'required';
                $this->rules['phone_number'] = 'required'; //regex:/^\d{8,15}$/
                $this->rules['full_phone_number'] = 'phone:AUTO';
            }
            $this->rules['email'] = 'required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|unique:customers,email,' . $this->id . ',id,deleted_at,NULL';
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
            'gender.required' => __('Please select gender'),
            'gender.in' => __('Please select valid gender'),
            'company_name.required_if' => __('Please enter company name'),
            'company_address.required_if' => __('Please enter comapany address'),
            'email.required' => __('Please enter email'),
            'email.email' => __('Please enter valid email'),
            'email.regex' => __('Email format is not valid'),
            'email.unique' => __('Email already exists'),
            'password.required' => __('Please enter password'),
            'password.min' => __('Please enter atleast 6 characters'),
            'password.regex' => __('Password should have at least 1 lowercase, 1 uppercase and 1 number'),
            'username.required' => __('Please enter username'),
            'username.min' => __('Please enter atleast 6 characters'),
            'username.unique' => __('Username already exists'),
            'user_type.required' => __('Please select user type'),
            'user_type.in' => __('Please select valid user type'),
            'country_code.required' => __('Please select country code'),
            'phone_number.required' => __('Please enter phone number'),
            'phone_number.regex' => __('Allow only 8 to 15 numbers.'),
            'country_id.required' => __('Please select country'),
            'city.required' => __('Please enter city'),
            'zipcode.required' => __('Please enter zipcode'),
            'zipcode.max' => __('Only allowed max 10 value'),
            'timezone_id.required' => __('Please select timezone'),
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
