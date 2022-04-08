<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckPhoneNumberRequest extends FormRequest
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
        $this->rules['country_code'] = 'required';
        $this->rules['phone_number'] = 'required'; //regex:/^\d{8,15}$/
        $this->rules['full_phone_number'] = 'phone:AUTO';

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
            'country_code.required' => __('Please select country code'),
            'phone_number.required' => __('Please enter phone number'),
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
