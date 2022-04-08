<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckEmailRequest extends FormRequest
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
        $uniqueValidation = '';
        switch ($this->user_type) {
            case '1':
                $uniqueValidation = "unique:customers,email,{$this->id},id,deleted_at,NULL";                
                break;
            case '2':
                $uniqueValidation = "unique:trainers,email,{$this->id},id,deleted_at,NULL";                
                break;
        }
        return [
            'user_type' => 'required|in:1,2',
            'email' => 'required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|' . $uniqueValidation,
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
            'user_type.required' => __('Please enter user type'),
            'user_type.in' => __('Please enter valid user type'),
            'email.required' => __('Please enter email'),
            'email.email' => __('Please enter valid email'),
            'email.regex' => __('Email format is not valid'),
            'email.unique' => __('Email already exists'),
        ];
    }
}
