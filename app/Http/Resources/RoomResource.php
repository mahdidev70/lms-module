<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RoomResource extends JsonResource
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
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'bannerUrl' => $this->banner_url,
            'avatarUrl' => $this->avatar_url,
            'membersCount' => count($this->members),
            'previewedMembers' => PreviewedMemberResource::collection($this->members),
            'category' => new CategoryResource($this->category)
        ];
    }
}
