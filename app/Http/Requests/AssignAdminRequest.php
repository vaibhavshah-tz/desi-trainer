<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssignAdminRequest extends FormRequest
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
        $this->rules['user_id'] = 'required_without_all:assign_to_self';
        $this->rules['assign_to_self'] = 'required_without_all:user_id';

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
            'user_id.required_without_all' => __('Please fill at least 1 of these fields.'),
            'assign_to_self.required_without_all' => __('Please fill at least 1 of these fields.'),
        ];
    }
}
