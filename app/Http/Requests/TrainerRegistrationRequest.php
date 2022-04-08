<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TrainerRegistrationRequest extends FormRequest
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
        $method = Request::method();
        if (!in_array($method, ['PATCH', 'PUT'])) {
            $this->rules['course_category_id'] = 'required|integer';
            $this->rules['certificate'] = 'mimes:jpeg,png,jpg,pdf|max:5120';
            $this->rules['skill_title'] = 'required';
            $this->rules['total_experience_year'] = 'required|integer';
            $this->rules['total_experience_month'] = 'integer';
            $this->rules['prior_teaching_experience_year'] = 'required|integer';
            $this->rules['prior_teaching_experience_month'] = 'integer';
            $this->rules['ticket_type'] = 'array';
            $this->rules['primary_skill'] = 'array';
            $this->rules['primary_skill.*'] = 'required';
            if (!$this->id) {
                $this->rules['password'] = 'required|min:6|same:password_confirmation|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/';
                $this->rules['password_confirmation'] = 'required';
            }
        }

        $this->rules['first_name'] = 'required';
        $this->rules['last_name'] = 'required';
        $this->rules['gender'] = 'required|integer';
        $this->rules['country_id'] = 'required|integer';
        $this->rules['city'] = 'required';
        $this->rules['timezone_id'] = 'required|integer';
        $this->rules['zipcode'] = 'required|max:10';

        if (Request::is('api/*')) {
            $id = !empty(\Auth::guard('trainer')->user()) ? \Auth::guard('trainer')->user()->id : $this->id;
            $this->rules['avatar'] = 'image|mimes:jpeg,png,jpg|max:5120';
            $this->rules['resume'] = 'mimes:doc,docx,pdf|max:5120';
            $this->rules['email'] = 'required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|unique:trainers,email,' . $id . ',id,deleted_at,NULL';
            $this->rules['country_code'] = 'required';
            $this->rules['phone_number'] = 'required'; //regex:/^\d{8,15}$/
            $this->rules['full_phone_number'] = 'phone:AUTO';
        } else {
            if(!$this->id || \Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
                $this->rules['country_code'] = 'required';
                $this->rules['phone_number'] = 'required'; //regex:/^\d{8,15}$/
                $this->rules['full_phone_number'] = 'phone:AUTO';
            }
            $this->rules['avatar'] = 'image|mimes:jpeg,png,jpg|max:5120';
            $this->rules['resume'] = 'mimes:doc,docx,pdf|max:5120';
            $this->rules['email'] = 'required|email|regex:/^\w+([\.+-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/|unique:trainers,email,' . $this->id . ',id,deleted_at,NULL';
            $this->rules['training_price'] = 'required|integer|min:1';
            $this->rules['job_support_price'] = 'required|integer|min:1';
            $this->rules['interview_support_price'] = 'required|integer|min:1';
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
            'country_id.required' => __('Please select country'),
            'country_id.integer' => __('Please select valid country'),
            'course_category_id.required' => __('Please select course category'),
            'course_category_id.integer' => __('Please select valid course category'),
            'timezone_id.required' => __('Please select timezone'),
            'timezone_id.integer' => __('Please select valid timezone'),
            'first_name.required' => __('Please enter first name'),
            'last_name.required' => __('Please enter last name'),
            'email.required' => __('Please enter email'),
            'email.email' => __('Please enter valid email'),
            'email.regex' => __('Email format is not valid'),
            'email.unique' => __('Email is already exist'),
            'password.required' => __('Please enter password'),
            'password.min' => __('Please enter minimum 6 characters'),
            'password.regex' => __('Password must at least 1 lower character, at least 1 capital character, at least 1 numeric characte'),
            'password_confirmation.confirmed' => __('Password and confirm password does not match'),
            'password_confirmation.required' => __('Please enter confirm password'),
            'username.required' => __('Please enter username'),
            'username.unique' => __('Please enter unique username'),
            'username.min' => __('Please enter minimum 6 characters'),
            'avatar.required' => __('Please select avatar'),
            'avatar.image' => __('Please select only image'),
            'avatar.mimes' => __('Avatar only accept file type JPG, JPEG, and PNG'),
            'avatar.max' => __('Maximum 5MB file are allowed'),
            'certificate.mimes' => __('Certificate only accept DOC, DOCX, and PDF'),
            'certificate.max' => __('Maximum 5MB file are allowed'),
            'country_code.required' => __('Please select country code'),
            'phone_number.required' => __('Please phone number'),
            'phone_number.regex' => __('Allow only 8 to 15 numbers.'),
            'city.required' => __('Please enter city'),
            'zipcode.required' => __('Please enter zipcode'),
            'zipcode.max' => __('Only allowed max 10 value'),
            'skill_title.required' => __('Please enter skill title'),
            'total_experience_year.required' => __('Please select total experience year'),
            'total_experience_year.integer' => __('Please select valid value'),
            'total_experience_year.max' => __('Please eneter maximum four digits'),
            'total_experience_year.min' => __('Please eneter maximum four digits'),
            'total_experience_month.required' => __('Please select total experience month'),
            'total_experience_month.integer' => __('Please select valid value'),
            'total_experience_month.max' => __('Please eneter maximum two digits'),
            'total_experience_month.min' => __('Please eneter maximum two digits'),
            'prior_teaching_experience_year.required' => __('Please select prior teaching experience year'),
            'prior_teaching_experience_year.integer' => __('Please select valid value'),
            'prior_teaching_experience_year.max' => __('Please eneter maximum four digits'),
            'prior_teaching_experience_year.min' => __('Please eneter maximum four digits'),
            'prior_teaching_experience_month.required' => __('Please select prior teaching experience month'),
            'prior_teaching_experience_month.integer' => __('Please select valid value'),
            'prior_teaching_experience_month.max' => __('Please eneter maximum two digits'),
            'prior_teaching_experience_month.min' => __('Please eneter maximum two digits'),
            'resume.required' => __('Please select resume'),
            'resume.mimes' => __('Resume only accept file type DOC, DOCX, and PDF'),
            'resume.max' => __('Maximum 5MB file are allowed'),
            'ticket_type.required' => __('Please select ticket type'),
            'ticket_type.array' => __('Ticket id must be in array'),
            'primary_skill.required' => __('Please select primary skill'),
            'primary_skill.array' => __('Primary skill must be in array'),
            'full_phone_number.phone' => __('Please enter valid number.'),
            'training_price.required' => __('Please enter training price'),
            'training_price.integer' => __('Please enter valid price'),
            'training_price.min' => __('The value can not be negative or 0 value'),
            'job_support_price.required' => __('Please enter job support price'),
            'job_support_price.integer' => __('Please enter valid price'),
            'job_support_price.min' => __('The value can not be negative or 0 value'),
            'interview_support_price.required' => __('Please enter interview support price'),
            'interview_support_price.integer' => __('Please enter valid price'),
            'interview_support_price.min' => __('The value can not be negative or 0 value'),
        ];
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'full_phone_number' => $this->country_code . $this->phone_number,
        ]);
    }
}
