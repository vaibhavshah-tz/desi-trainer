<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class InstallmentRequest extends FormRequest
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
        $this->rules['name'] = 'required';
        $this->rules['amount'] = 'required|numeric';
        $this->rules['due_date'] = 'required|date|date_format:Y-m-d';

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
            'name.required' => __('Please enter name'),
            'due_date.required' => __('Please select due date'),
            'due_date.date' => __('Please select valid due date'),
            'amount.required' => __('Please enter amount'),
            'amount.numeric' => __('Please enter valid amount'),
        ];
    }
}
