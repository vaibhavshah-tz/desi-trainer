<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class EditInvoiceRequest extends FormRequest
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
        $this->rules['payment_status'] = 'required';
        $this->rules['file'] = 'image|mimes:jpeg,png,jpg|max:5120';

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
            'payment_status.required' => __('Please select status'),
            'file.image' => __('Please select only image'),
            'file.mimes' => __('File only accept file type JPG, JPEG, and PNG'),
            'file.max' => __('Maximum 5MB file are allowed'),
        ];
    }
}
