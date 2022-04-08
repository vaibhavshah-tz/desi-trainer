<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseCategoryRequest extends FormRequest
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
        $this->rules['status'] = 'required';

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
            'status.required' => __('Please select status'),
        ];
    }
}
