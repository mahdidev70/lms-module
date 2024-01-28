<?php

namespace TechStudio\Lms\app\Http\Resources;

use Exception;
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
        // try{
        //     optional($this->load('courses.comments'));
        // }catch(Exception $e){}


        return [
            'id' => optional($this)->user_id,
            'type' => $this->getUserType(),
            'displayName' => optional($this)->getDisplayName(),
            'avatarUrl' => optional($this)->avatar_url,
            'description' => optional($this)->description,
            'status' => optional($this)->status,
            'commentCount' => optional($this)->courses->flatMap->comments->count(),
            'courseCount' => optional($this)->courses->count(),
        ];
    }
}
