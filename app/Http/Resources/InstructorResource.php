<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $this->load('courses.comments'); 

        return [
            'id' => $this->id,
            'type' => $this->getUserType(),
            'displayName' => $this->getDisplayName(),
            'avatarUrl' => $this->avatar_url,
            'description' => $this->description,
            'status' => $this->status,
            'commentCount' => $this->courses->flatMap->comments->count(),
            'courseCount' => $this->courses->count(),
        ];
    }
}
