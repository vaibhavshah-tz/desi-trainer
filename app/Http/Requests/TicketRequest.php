<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TicketRequest extends FormRequest
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
        if (!Request::is('api/*')) {
            $this->rules['user_id'] = 'required';
        } else {
            $this->rules['customer_id'] = 'required';
            $this->rules['is_for_employee'] = 'in:0,1';
            $this->rules['employees'] = 'required_if:is_for_employee,1|array';
            $this->rules['employees.*.employee_name'] = 'required_if:is_for_employee,1';
            $this->rules['employees.*.country_code'] = 'required_if:is_for_employee,1';
            $this->rules['employees.*.phone_number'] = 'required_if:is_for_employee,1||regex:/^\d{8,15}$/';
            $this->rules['employees.*.email'] = 'required_if:is_for_employee,1|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/';
        }

        $this->rules['ticket_type_id'] = 'required';
        $this->rules['course_category_id'] = 'required';
        $this->rules['course_id'] = 'required';
        $this->rules['primary_skill'] = 'array';
        $this->rules['primary_skill.*'] = 'required';
        $this->rules['date'] = 'required|date_format:Y-m-d';
        $this->rules['time'] = 'required|date_format:H:i:s';
        $this->rules['timezone_id'] = 'required';

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
            'customer_id.required' => __('Please pass customer id'),
            'ticket_type_id.required' => __('Please select ticket type'),
            'course_category_id.required' => __('Please select course category'),
            'course_id.required' => __('Please select course'),
            'primary_skill.required' => __('Please select primary skill'),
            'primary_skill.array' => __('Primary skill must be in array'),
            'timezone_id.required' => __('Please select timezone'),
            'date.required' => __('Please select date'),
            'date.date_format' => __('Date must be in Y-m-d format'),
            'time.required_if' => __('Please select time'),
            'time.date_format' => __('Time must be in H:i:s format'),
            'is_for_employee.in' => __('Please enter valid data'),
            'employees.required_if' => __('Please enter employees data'),
            'employees.array' => __('Employees must be in array'),
            'employees.*.employee_name.required_if' => __('Please enter employee name'),
            'employees.*.country_code.required_if' => __('Please enter country code'),
            'employees.*.phone_number.required_if' => __('Please enter phone number'),
            'employees.*.phone_number.regex' => __('Allow only 8 to 15 numbers'),
            'employees.*.email.required_if' => __('Please enter email'),
            'employees.*.email.email' => __('Please enter valid email'),
            'employees.*.email.regex' => __('Email format is not valid'),
        ];
    }
}
