<?php

namespace TechStudio\Lms\app\Http\Request;

use Illuminate\Foundation\Http\FormRequest;

class BookmarkRequest extends FormRequest
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
            'courseId' => 'required|integer|exists:courses,id',
            'bookmark' => 'required|boolean',
        ];
    }
}
