<?php

namespace App\Http\Resources\Lms;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomePageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'landingBannerUrl' => $this->landingBannerUrl,
            'categories' => CategoryCoursesResource::collection($this->categories),
            // 'outPutSqureUpBannerUrl' => $this->outPutSqureUpBannerUrl,
            // 'outPutSqureDownBannerUrl' =>   $this->outPutSqureDownBannerUrl,
            // 'outPutRectangleBannerUrl' =>   $this->outPutRectangleBannerUrl,
            'comments' =>   CommentResource::collection($this->comments),
        ];
    }
}
