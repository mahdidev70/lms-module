<?php

namespace App\Http\Requests\Lms;

use Illuminate\Foundation\Http\FormRequest;

class LessonCreateUpdateRequest extends FormRequest
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
            //required fields to createUpdate a lesson: 
            'title' => 'required|string',
            'dominantType' => 'required|in:text,video,exam',
            'chapterId' => 'required|integer',
            'content' => 'required|array',
            // Optional fields to createUpdate a lesson:
            'answers' => 'array',// if dominantType = exam
            'order' => 'integer',
        ];
    }
}
