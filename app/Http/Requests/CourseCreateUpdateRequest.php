<?php

namespace TechStudio\Lms\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseCreateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //required fields to create a course: 
            'title' => 'required|string',
            'slug' => 'string', 
            'description' => 'required|string',
            'categoryId' => 'integer',
            'instructor' => 'required|array',
            // Optional fields to create a course:
            'bannerUrl' => 'string',
            'bannerUrlMobile' => 'string',
            'languages' => 'array',
            'level' => 'in:beginner,intermediate,advance',
            'supportItem' => 'array',
            'learningPoints*.title' => 'required|string',
            'learningPoints*.description' => 'string',
            'features' => 'array',
            'faq' => 'array',
            'supportItems' => 'array',
            'skillsId' => 'array',
            'prerequisites' => 'array',
            'prerequisites.*' => 'exists:courses,id' 
        ];
    }
}
