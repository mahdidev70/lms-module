<?php

namespace TechStudio\Lms\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
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
            'courseId' => 'required|integer|exists:lms_courses,id',
            'rate' => 'required|integer|between:1,10',
            'comment' => 'string',
        ];
    }
}
