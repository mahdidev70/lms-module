<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Http\Resources\CoursePreviewResource;

class CategoryCoursesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'skills' => json_decode($this->skills),
            'UserEnrolledCount' => 10,
            'courseCount' => count($this->courses),
            'courses' => CoursePreviewResource::collection($this->courses),
        ];
    }
}
