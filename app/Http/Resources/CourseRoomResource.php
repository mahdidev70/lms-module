<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseRoomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instructor' => new InstructorResource($this->instructor),
            'createAt' => $this->created_at,
            'rooms' => RoomResource::collection($this->rooms),
            'sidebar' => new CourseSidebarResource($this),
        ];
    }
}
