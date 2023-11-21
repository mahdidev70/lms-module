<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Http\Resources\CategoryResource;
use TechStudio\Lms\app\Http\Resources\InstructorResource;

class FiltersCourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'categories' => CategoryResource::collection($this->categories),
            'skills' => SkillResource::collection($this->skills),
            'instructors' => InstructorResource::collection($this->instructors)
        ];
    }
}
