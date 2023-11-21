<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserHomeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new InstructorResource($this->user),
            'userCourses' => [
                'necessary' => CoursePreviewResource::collection($this->necessary),
                'progress' => CoursePreviewResource::collection($this->progress),
                'done' => CoursePreviewResource::collection($this->done),
                'bookmarks' => CoursePreviewResource::collection($this->bookmarks),
            ],
            'categories' => CategoryCoursesResource::collection($this->categories),
            'comments' =>   CommentResource::collection($this->comments),
            'topCourses' => CoursePreviewResource::collection($this->topCourses),
            'recentlyVisitedCourses' => CoursePreviewResource::collection($this->recentlyVisitedCourses)
        ];
    }
}
