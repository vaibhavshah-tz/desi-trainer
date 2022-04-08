<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailTemplateRequest extends FormRequest
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
            'name' => "required|unique:email_templates,name,{$this->id},id",
            'subject' => 'required',
            'body' => 'required'
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
            'name.required' => 'Please enter name',
            'name.unique' => 'Name already exists',
            'subject.required' => 'Please enter subject',
            'body.required' => 'Please enter body'
        ];
    }
}
