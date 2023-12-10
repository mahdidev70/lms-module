<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'avatarUrl' => optional($this->userProfile)->avatar_url,
            'role' => 'کاربر عادی',
            'displayName' => optional($this->userProfile)->getDisplayName(),
            'progress_count' => $this->where('in_roll', 'progress')->count(),
            'done_count' => $this->where('in_roll', 'done')->count(),
            'bookmark_count' => $this->where('bookmark', '1')->count(),
            // 'necessary_count' => $courseCount,
        ];
    }
}
