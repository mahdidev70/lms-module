<?php

namespace App\Http\Resources\Lms;

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
        return [
            'id' => $this->id,
            'type' => $this->getUserType(),
            'displayName' => $this->getDisplayName(),
            'avatarUrl' => $this->avatar_url,
            'description' => $this->description
        ];
    }
}
