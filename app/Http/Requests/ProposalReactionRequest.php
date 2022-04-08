<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class ProposalReactionRequest extends FormRequest
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
        $this->rules['action'] = 'required|in:1,2';
        $this->rules['denied_reason'] = 'required_if:action,2';

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
            'action.required' => __('Please enter action'),
            'action.in' => __('Please enter valid action'),
            'denied_reason.required_if' => __('Please enter denied reason'),
        ];
    }
}
