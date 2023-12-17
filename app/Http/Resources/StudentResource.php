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
            'avatarUrl' => $this->avatar_url,
            'role' => 'کاربر عادی',
            'displayName' => $this->getDisplayName(),
            "progress_count" => $this->progressCount,
            "done_count" => $this->doneCount,
            "bookmark_count" => $this->bookmarkCount,
            // "necessary_count" => 1,
        ];
    }
}
