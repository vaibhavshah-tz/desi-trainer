<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProposalRequest extends FormRequest
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
        $this->rules['description'] = 'required';
        $this->rules['currency'] = 'required';
        $this->rules['quote'] = 'required|numeric';
        if(!$this->proposal_id) {
            $this->rules['ids'] = 'required';
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
            'name.required' => __('Please enter name'),
            'description.required' => __('Please enter description'),
            'currency.required' => __('Please select currency'),
            'quote.required' => __('Please enter quote'),
            'quote.numeric' => __('Please enter valid quote'),
            'ids.required' => __('Please select atleast one trainer'),
        ];
    }
}
