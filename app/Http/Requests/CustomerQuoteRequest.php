<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class CustomerQuoteRequest extends FormRequest
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
        $this->rules['currency'] = 'required';
        $this->rules['quote'] = 'required|numeric';

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
            'currency.required' => __('Please select currency'),
            'quote.required' => __('Please enter final pricing'),
            'quote.numeric' => __('Please enter valid final pricing'),
        ];
    }
}
