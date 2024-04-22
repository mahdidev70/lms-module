<?php

namespace TechStudio\Lms\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'id' => ['nullable', 'integer'],
            'title' => ['string', 'required', Rule::unique('lms_lessons')->ignore($this->id)],
            'slug' => ['string', Rule::unique('lms_lessons')->ignore($this->id)],
            'dominantType' => 'required|in:text,video,exam',
            'chapterId' => 'required|integer',
            'content' => 'required|array',
            // Optional fields to createUpdate a lesson:
            'answers' => 'array',// if dominantType = exam
            'order' => 'integer',
        ];
    }
}
