<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class MeetingRequest extends FormRequest
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
        if (Request::is('api/*')) {
            $this->rules['ticket_id'] = 'required|integer';
            $this->rules['customer_id'] = 'required|integer';
        } else {
            $this->rules['create_meeting_with'] = 'required';
            $this->rules['interested_trainer_id'] = 'required_if:create_meeting_with,'.config('constants.MEETING.CREATE_WITH.INTERESTED_TRAINER').','.config('constants.MEETING.CREATE_WITH.CUSTOMER_AND_INTERESTED_TRAINER');
        }
        $this->rules['timezone_id'] = 'required|integer';
        $this->rules['meeting_title'] = 'required';
        $this->rules['date'] = 'required|date|date_format:Y-m-d';
        $this->rules['time'] = 'required|date_format:H:i:s';
        $this->rules['meeting_url'] = 'required|url';

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
            'timezone_id.required' => __('Please select timezone'),
            'timezone_id.integer' => __('Please select valid timezone'),
            'ticket_id.required' => __('Please enter ticket id'),
            'ticket_id.integer' => __('Please enter valid ticket id'),
            'customer_id.required' => __('Please enter customer id'),
            'customer_id.integer' => __('Please enter valid customer id'),
            'meeting_title.required' => __('Please enter meeting title'),
            'date.required' => __('Please enter date'),
            'date.date' => __('Please enter valid date'),
            'time.required' => __('Please enter time'),
            'time.date_format' => __('Please enter valid time'),
            'meeting_url.required' => __('Please enter meeting url'),
            'meeting_url.url' => __('Please enter valid meeting url'),
            'interested_trainer_id.required_if' => __('Please select interested trainer'),
        ];
    }
}
