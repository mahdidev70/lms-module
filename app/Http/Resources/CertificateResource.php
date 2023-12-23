<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            'displayName' => $this->userProfile->getDisplayName(),
            'avatarUrl' => $this->avatar_url,
            'course' => ($this->course)?$this->course->title:null,
            'category' => ($this->course)?$this->course->category->title:null,
            'finalScore' => 98,
            'certificateFile' => 'https://storage.sa-test.techstudio.diginext.ir/static/amirmahdi.jpg',
        ];
    }
}
