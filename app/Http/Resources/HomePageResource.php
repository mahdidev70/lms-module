<?php

namespace TechStudio\Lms\app\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Http\Resources\CategoryCoursesResource;
use TechStudio\Lms\app\Http\Resources\CommentResource;

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
