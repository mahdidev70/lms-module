<?php

namespace TechStudio\Lms\app\Http\Resources;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;
use TechStudio\Lms\app\Http\Resources\InstructorResource;

class CoursePreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $studentCount = sizeof($this->students) >0 ?$this->students()->count() : 0;
        $rateCount = sizeof($this->students) >0 ? $this->students()->where('rate','>=',0)->count() : 0;
        $rateSum = sizeof($this->students) >0 ? $this->students()->where('rate','>=',0)->sum('rate') : 0;
        $commentCount = sizeof($this->comments) >0 ? $this->comments()->count():0;
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'instructor' => new InstructorResource($this->instructor),
            'thumbnail_url' => $this->thumbnail_url,
            'bannerUrl' => $this->banner_url,
            'bannerUrlMobile' => $this->banner_url_mobile,
            'participantsCount' => $studentCount,
            'totalDurationMinutes' => $this->duration,
            'totalDuration' => $this->duration?$this->getTotalDuration():0,
            'videosCount' => $this->videos_count,
            'languages' => json_decode($this->languages),
            'ratingsCount' => $rateCount,
            'averageRating' => $rateSum ?? 0 / $rateCount ?? 0,
            'commentCount' => $commentCount,
            'level' => $this->level,
            'certificateEnabled' => $this->certificate_enabled,
            'instructorSupport' => $this->instructor_support,
            'moneyReturnGuarantee' => $this->money_return_guarantee,
            'createAt' => $this->created_at
        ];
    }
}
