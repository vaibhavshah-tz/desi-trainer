<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatReadRequest extends FormRequest
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
        $this->rules['chat_room_id'] = 'required|integer';
        $this->rules['is_read'] = 'required|boolean';
        $this->rules['receiver_type'] = 'required|integer';

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
            'chat_room_id.required' => __('Please enter chat room id.'),
            'chat_room_id.integer' => __('Please enter valid value'),
            'is_read.required' => __('Please enter is read value'),
            'is_read.boolean' => __('Please enter valid value'),
            'receiver_type.required' => __('Please enter receiver type id.'),
            'receiver_type.integer' => __('Please enter valid value'),
        ];
    }
}
