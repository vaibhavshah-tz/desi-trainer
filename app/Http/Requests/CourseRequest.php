<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CourseRequest extends FormRequest
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
     * Return validation errors as json response
     *
     * @param Validator $validator
     */
    protected function failedValidation(Validator $validator)
    {
        $response = [
            // 'status' => 'failure',
            'status' => 400,
            'message' => 'Bad Request',
            'errors' => $validator->errors(),
        ];

        throw new HttpResponseException(response()->json($response, 400));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->rules['course_category_id'] = 'required';
        $this->rules['name'] = 'required';
        $this->rules['description'] = 'required';
        $this->rules['course_price'] = 'required|numeric';
        $this->rules['course_special_price'] = 'required|numeric';
        if ($this->id) {
            $this->rules['cover_image'] = 'image|mimes:jpeg,png,jpg|max:5120';
        } else {
            $this->rules['cover_image'] = 'required|image|mimes:jpeg,png,jpg|max:5120';
        }

        $this->rules['currency'] = 'required';

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
            'course_category_id.required' => __('Please select course category'),
            'name.required' => __('Please enter name'),
            'description.required' => __('Please enter description'),
            'course_price.required' => __('Please enter course pricw'),
            'course_price.numeric' => __('Please enter valid course price'),
            'course_special_price.required' => __('Please enter course special price'),
            'course_special_price.numeric' => __('Please enter valid course special price'),
            'cover_image.required' => __('Please select cover image'),
            'cover_image.image' => __('Please select only image'),
            'cover_image.mimes' => __('Cover image only accept file type JPG, JPEG, and PNG'),
            'cover_image.max' => __('Maximum 5MB file are allowed'),
            'currency.required' => __('Please enter currency'),
        ];
    }
}
