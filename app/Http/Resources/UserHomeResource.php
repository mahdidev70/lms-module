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
                'necessary' => ($this->necessary && sizeof($this->necessary) > 0 )?CoursePreviewResource::collection($this->necessary):[],
                'progress' =>  ($this->progress && sizeof($this->progress) > 0 )? CoursePreviewResource::collection($this->progress):[],
                'done' => ($this->done && sizeof($this->done) > 0 )? CoursePreviewResource::collection($this->done):[],
                'bookmarks' => ($this->bookmarks && sizeof($this->bookmarks) > 0 )? CoursePreviewResource::collection($this->bookmarks):[],
            ],
            'categories' => CategoryCoursesResource::collection($this->categories),
            'comments' =>   CommentResource::collection($this->comments),
            'topCourses' => ($this->topCourses && sizeof($this->topCourses) > 0 )?CoursePreviewResource::collection($this->topCourses):[],
            'recentlyVisitedCourses' => ($this->recentlyVisitedCourses && sizeof($this->recentlyVisitedCourses) > 0 )?CoursePreviewResource::collection($this->recentlyVisitedCourses):null
        ];
    }
}
