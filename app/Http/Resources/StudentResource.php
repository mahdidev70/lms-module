<?php

namespace App\Http\Resources\Lms;

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

            'rate' => $this->rate,

            // 'progress_count' => $this->in_roll->where('in_roll', 'progress')->count(),

            // 'done_count' => $this->in_roll->where('in_roll', 'done')->count(),

            // 'bookmark_count' => $this->bookmark->count(),

            // 'necessary_count' => optional($this->course)->necessary->count(),
        ];
    }
}
